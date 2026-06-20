<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    /**
     * POST /api/tickets
     * User memesan tiket untuk sebuah event.
     * Status awal: pending
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $userId  = $request->attributes->get('auth_user_id');
        $eventId = $request->input('event_id');
        $qty     = $request->input('quantity');

        // Ambil data event dari Event Service
        try {
            $eventResponse = Http::get(env('EVENT_SERVICE_URL') . '/graphql', [
                'query' => "{ event(id: {$eventId}) { id name price stock } }"
            ]);

            if ($eventResponse->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data event.',
                ], 502);
            }

            $event = $eventResponse->json('data.event');

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event tidak ditemukan.',
                ], 404);
            }

            // Cek stok
            if ($event['stock'] < $qty) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok tidak mencukupi. Sisa stok: {$event['stock']}.",
                ], 409);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung ke Event Service.',
            ], 503);
        }

        // Hitung total harga
        $totalPrice = $event['price'] * $qty;

        // Buat record tiket dengan status pending
        $ticket = Ticket::create([
            'user_id'     => $userId,
            'event_id'    => $eventId,
            'quantity'    => $qty,
            'total_price' => $totalPrice,
            'status'      => 'pending',
        ]);

        // Kurangi stok di Event Service
        try {
            Http::patch(env('EVENT_SERVICE_URL') . "/api/events/{$eventId}/stock", [
                'reduce_by' => $qty,
            ]);
        } catch (\Exception $e) {
            // Log saja, tidak perlu rollback — bisa ditangani dengan saga pattern
            \Log::warning("Gagal update stok event #{$eventId}: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Tiket berhasil dipesan. Silakan lanjutkan ke pembayaran.',
            'data'    => $ticket,
        ], 201);
    }

    /**
     * GET /api/tickets
     * Lihat semua tiket milik user yang sedang login.
     */
    public function index(Request $request)
    {
        $userId  = $request->attributes->get('auth_user_id');

        $tickets = Ticket::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $tickets,
        ]);
    }

    /**
     * GET /api/tickets/{id}
     * Detail satu tiket beserta kode tiket jika sudah confirmed.
     */
    public function show(Request $request, int $id)
    {
        $userId = $request->attributes->get('auth_user_id');

        $ticket = Ticket::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $ticket,
        ]);
    }
}

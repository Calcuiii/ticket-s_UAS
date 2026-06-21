<?php
namespace App\Http\Controllers;
use App\Models\Transaction;
use App\Models\PaymentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    // POST /api/payments
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id'      => 'required|integer',
            'amount'         => 'required|numeric',
            'payment_method' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // Ambil data ticket dari Ticket Service untuk cek total_price
        $token = $request->bearerToken();

        $ticketResponse = Http::withToken($token)
            ->get('http://ticket-service:8000/api/' . $request->ticket_id);

        if (!$ticketResponse->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket tidak ditemukan.',
            ], 404);
        }

        $ticket = $ticketResponse->json('data');

        // Validasi: amount tidak boleh kurang dari total_price tiket
        if ($request->amount < $ticket['total_price']) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah pembayaran (' . $request->amount . ') kurang dari total harga tiket (' . $ticket['total_price'] . ').',
            ], 422);
        }

        // Simpan transaksi dengan status pending dulu
        $transaction = Transaction::create([
            'ticket_id'      => $request->ticket_id,
            'user_id'        => $request->auth_user_id,
            'amount'         => $request->amount,
            'status'         => 'pending',
            'payment_method' => $request->payment_method,
        ]);

        // Simpan log
        PaymentLog::create([
            'transaction_id' => $transaction->id,
            'message'        => 'Transaksi dibuat, menunggu pembayaran.',
        ]);

        // Simulasi pembayaran selalu sukses
        $isSuccess = $request->input('simulate_success', true);
        $status = $isSuccess ? 'success' : 'failed';
        $transaction->update(['status' => $status]);

        PaymentLog::create([
            'transaction_id' => $transaction->id,
            'message' => $isSuccess
                ? 'Pembayaran berhasil dikonfirmasi.'
                : 'Pembayaran gagal, saldo tidak mencukupi.',
        ]);

        // Publish ke Redis
        if ($isSuccess) {
            $count = Redis::publish('payment_success', json_encode([
                'ticket_id'      => $transaction->ticket_id,
                'transaction_id' => $transaction->id,
                'user_id'        => $request->auth_user_id,
            ]));
            \Log::info('JUMLAH SUBSCRIBER = ' . $count);
        }

        return response()->json([
            'success'     => $isSuccess,
            'message'     => $isSuccess ? 'Pembayaran berhasil.' : 'Pembayaran gagal.',
            'transaction' => $transaction,
        ], $isSuccess ? 201 : 400);
    }

    // GET /api/payments/{id}
    public function show($id)
    {
        $transaction = Transaction::with('logs')->find($id);
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan.',
            ], 404);
        }
        return response()->json([
            'success'     => true,
            'transaction' => $transaction,
        ]);
    }

    // GET /api/payments/history
    public function history(Request $request)
    {
        $transactions = Transaction::with('logs')
            ->where('user_id', $request->query('user_id'))
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            'success'      => true,
            'transactions' => $transactions,
        ]);
    }
}
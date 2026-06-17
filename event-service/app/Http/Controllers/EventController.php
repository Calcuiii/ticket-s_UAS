<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Category;

class EventController extends Controller
{
    // GET /api/events
    public function index(Request $request)
    {
        $query = Event::with('category')
            ->where('status', 'published');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->has('date_from')) {
            $query->whereDate('event_date', '>=', $request->date_from);
        }
        $events = $query->orderBy('event_date', 'asc')
            ->paginate($request->get('per_page', 10));

        return response()->json([
            'status' => true,
            'data'   => $events
        ]);
    }

    // POST /api/events
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'    => 'required|exists:categories,id',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'location'       => 'required|string',
            'event_date'     => 'required|date|after:now',
            'event_end_date' => 'nullable|date|after:event_date',
            'ticket_price'   => 'required|numeric|min:0',
            'total_stock'    => 'required|integer|min:1',
            'organizer_name' => 'required|string',
            'banner_url'     => 'nullable|url',
            'status'         => 'sometimes|in:draft,published',
        ]);
        $validated['slug']            = Str::slug($validated['title']) . '-' . time();
        $validated['available_stock'] = $validated['total_stock'];

        $event = Event::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Event created',
            'data'    => $event->load('category')
        ], 201);
    }

    // GET /api/events/{id}
    public function show($id)
    {
        $event = Event::with('category')->find($id);

        if (!$event) {
            return response()->json([
                'status'  => false,
                'message' => 'Event dengan ID ' . $id . ' tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $event
        ]);
        
    }

    // GET /api/categories/{id}/events
    public function getByCategory($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status'  => false,
                'message' => 'Kategori dengan ID ' . $id . ' tidak ditemukan'
            ], 404);
        }

        $events = Event::with('category')
            ->where('category_id', $id)
            ->where('status', 'published')
            ->orderBy('event_date', 'asc')
            ->get();

        if ($events->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => 'Tidak ada event pada kategori ' . $category->name
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Event kategori ' . $category->name,
            'data'    => $events
        ]);
    }


    // PUT /api/events/{id}
    public function update(Request $request, $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'status'  => false,
                'message' => 'Event dengan ID ' . $id . ' tidak ditemukan, tidak bisa melakukan edit'
            ], 404);
        }

        $validated = $request->validate([
            'category_id'    => 'sometimes|exists:categories,id',
            'title'          => 'sometimes|string|max:255',
            'description'    => 'nullable|string',
            'location'       => 'sometimes|string',
            'event_date'     => 'sometimes|date',
            'event_end_date' => 'nullable|date',
            'ticket_price'   => 'sometimes|numeric|min:0',
            'total_stock'    => 'sometimes|integer|min:1',
            'organizer_name' => 'sometimes|string',
            'status'         => 'sometimes|in:draft,published,cancelled,completed',
        ]);

        $event->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Event berhasil diupdate',
            'data'    => $event->load('category')
        ]);
    }

    // DELETE /api/events/{id}
    public function destroy($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'status'  => false,
                'message' => 'Event dengan ID ' . $id . ' tidak ditemukan'
            ], 404);
        }

        $event->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Event berhasil dihapus'
        ]);
    }

     public function decreaseStock(Request $request, $id)
    {
        // Validasi internal secret agar tidak bisa diakses sembarangan
        $secret = $request->header('X-Internal-Secret');
        if ($secret !== config('app.internal_secret')) {
            return response()->json(['status' => false, 'message' => 'Forbidden'], 403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $event = Event::findOrFail($id);
        if (!$event->decreaseStock($request->quantity)) {
            return response()->json([
                'status'  => false,
                'message' => 'Insufficient stock',
                'available_stock' => $event->available_stock
            ], 422);
        }

        return response()->json([
            'status'          => true,
            'message'         => 'Stock decreased',
            'available_stock' => $event->fresh()->available_stock
        ]);
    }
}

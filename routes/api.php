<?php

use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Ticket Service - API Routes
|--------------------------------------------------------------------------
|
| Semua route di sini dilindungi middleware JWT.
| Token diperoleh dari User Service setelah login.
|
*/

Route::middleware('jwt.auth')->group(function () {
    // POST   /api/tickets         → Pesan tiket baru (status: pending)
    // GET    /api/tickets         → Lihat semua tiket milik user
    // GET    /api/tickets/{id}    → Detail tiket + kode tiket jika confirmed
    Route::post('/', [TicketController::class, 'store']);
    Route::get('/', [TicketController::class, 'index']);
    Route::get('/{id}', [TicketController::class, 'show']);
});

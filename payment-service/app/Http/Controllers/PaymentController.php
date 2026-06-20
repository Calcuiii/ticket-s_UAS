<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\PaymentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    // POST /api/payments
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id'      => 'required|integer',
            'user_id'        => 'required|integer',
            'amount'         => 'required|numeric',
            'payment_method' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // Simpan transaksi dengan status pending dulu
        $transaction = Transaction::create([
            'ticket_id'      => $request->ticket_id,
            'user_id'        => $request->user_id,
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
            Redis::publish('payment_success', json_encode([
                'ticket_id'      => $transaction->ticket_id,
                'transaction_id' => $transaction->id,
                'user_id'        => $transaction->user_id,
            ]));
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
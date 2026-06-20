<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Predis\Client as RedisClient;

class RedisSubscriberCommand extends Command
{
    protected $signature   = 'redis:subscribe';
    protected $description = 'Subscribe ke Redis channel untuk menerima event dari Payment Service';

    public function handle(): void
    {
        $this->info('[Ticket Service] Memulai Redis Subscriber...');
        $this->info('[Ticket Service] Mendengarkan channel: payment_success, payment_failed');

        $redis = new RedisClient([
            'scheme'   => 'tcp',
            'host'     => env('REDIS_HOST', 'redis'),
            'port'     => env('REDIS_PORT', 6379),
            'password' => env('REDIS_PASSWORD', null),
        ]);

        $redis->subscribe(['payment_success', 'payment_failed'], function ($message, $channel) {
            $this->info("[{$channel}] Pesan diterima: {$message}");

            $payload = json_decode($message, true);

            if (!$payload || !isset($payload['ticket_id'])) {
                Log::warning("[$channel] Payload tidak valid: {$message}");
                return;
            }

            $ticket = Ticket::find($payload['ticket_id']);

            if (!$ticket) {
                Log::warning("[$channel] Tiket #{$payload['ticket_id']} tidak ditemukan.");
                return;
            }

            if ($channel === 'payment_success') {
                $this->handlePaymentSuccess($ticket, $payload);
            } elseif ($channel === 'payment_failed') {
                $this->handlePaymentFailed($ticket, $payload);
            }
        });
    }

    /**
     * Saat payment sukses:
     * - Update status tiket → confirmed
     * - Set transaction_id
     * - Generate kode tiket unik
     */
    private function handlePaymentSuccess(Ticket $ticket, array $payload): void
    {
        if ($ticket->status !== 'pending') {
            Log::info("Tiket #{$ticket->id} sudah diproses sebelumnya (status: {$ticket->status}). Dilewati.");
            return;
        }

        $ticket->update([
            'status'         => 'confirmed',
            'transaction_id' => $payload['transaction_id'] ?? null,
            'ticket_code'    => Ticket::generateTicketCode(),
        ]);

        $this->info("[payment_success] Tiket #{$ticket->id} berhasil dikonfirmasi. Kode: {$ticket->ticket_code}");
        Log::info("Tiket #{$ticket->id} confirmed. Kode tiket: {$ticket->ticket_code}");
    }

    /**
     * Saat payment gagal:
     * - Update status tiket → cancelled
     * - (Opsional) publish event untuk restore stok di Event Service
     */
    private function handlePaymentFailed(Ticket $ticket, array $payload): void
    {
        if ($ticket->status !== 'pending') {
            return;
        }

        $ticket->update(['status' => 'cancelled']);

        $this->info("[payment_failed] Tiket #{$ticket->id} dibatalkan.");
        Log::info("Tiket #{$ticket->id} cancelled karena pembayaran gagal.");
    }
}

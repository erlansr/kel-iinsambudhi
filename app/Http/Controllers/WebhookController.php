<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Midtrans Webhook Received', $request->all());
        
        $payload = $request->all();
        
        // Verifikasi signature
        if (!$this->verifySignature($payload)) {
            Log::warning('Invalid webhook signature', $payload);
            return response()->json(['error' => 'Invalid signature'], 401);
        }
        
        // Cek status pembayaran
        if ($payload['transaction_status'] === 'settlement') {
            $orderId = $payload['order_id'];
            
            // Cari pembayaran berdasarkan order_id
            $pembayaran = Pembayaran::where('midtrans_order_id', $orderId)->first();
            
            if ($pembayaran && $pembayaran->status !== 'lunas') {
                $pembayaran->update([
                    'status' => 'lunas',
                    'transaction_id' => $payload['transaction_id'],
                    'paid_at' => now(),
                    'payment_method' => 'qris'
                ]);
                
                Log::info('Payment updated successfully', [
                    'pembayaran_id' => $pembayaran->id,
                    'order_id' => $orderId
                ]);
            }
        }
        
        return response()->json(['success' => true]);
    }
    
    private function verifySignature($payload)
    {
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';
        
        $calculatedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . config('midtrans.server_key'));
        
        return $calculatedSignature === $signatureKey;
    }
}
<?php
// app/Http/Controllers/User/PaymentController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }
    
    public function index()
    {
        $keluargaList = Keluarga::all();
        return view('user.index', compact('keluargaList'));
    }
    
    public function tagihan(Keluarga $keluarga)
    {
        // Ambil semua pembayaran keluarga ini
        $pembayaran = Pembayaran::where('keluarga_id', $keluarga->id)->get();
        
        // CEK DAN UPDATE STATUS DARI MIDTRANS LANGSUNG
        foreach ($pembayaran as $tagihan) {
            // Cek hanya yang belum lunas dan punya midtrans_order_id
            if ($tagihan->status == 'belum_lunas' && $tagihan->midtrans_order_id) {
                try {
                    $status = Transaction::status($tagihan->midtrans_order_id);
                    
                    if ($status->transaction_status == 'settlement') {
                        $tagihan->update([
                            'status' => 'lunas',
                            'paid_at' => now(),
                            'payment_method' => 'qris',
                            'transaction_id' => $status->transaction_id
                        ]);
                        \Log::info('Status updated via direct check for ID: ' . $tagihan->id);
                    }
                } catch (\Exception $e) {
                    \Log::error('Check status error: ' . $e->getMessage());
                }
            }
        }
        
        // Ambil ulang setelah update
        $pembayaran = Pembayaran::where('keluarga_id', $keluarga->id)->get();
        
        return view('user.tagihan', compact('keluarga', 'pembayaran'));
    }
    
    public function generateQris(Pembayaran $pembayaran)
    {
        try {
            if ($pembayaran->status == 'lunas') {
                return response()->json(['success' => false, 'error' => 'Sudah lunas'], 400);
            }
            
            $orderId = 'KAS-' . $pembayaran->id . '-' . time();
            
            // PASTIKAN NOMINAL INTEGER (BUKAN DECIMAL)
            $nominal = (int) $pembayaran->nominal;
            
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $nominal,  // <- integer
                ],
                'customer_details' => [
                    'first_name' => $pembayaran->keluarga->nama_keluarga,
                    'email' => $pembayaran->keluarga->email ?? 'customer@example.com',
                    'phone' => $pembayaran->keluarga->no_telepon ?? '08123456789',
                ],
                'item_details' => [
                    [
                        'id' => (string) $pembayaran->id,
                        'price' => $nominal,  // <- integer
                        'quantity' => 1,
                        'name' => 'Kas RT - ' . $pembayaran->bulan,
                    ]
                ]
            ];
            
            \Log::info('Midtrans params:', $params);
            
            $snap = Snap::createTransaction($params);
            
            $pembayaran->update([
                'midtrans_order_id' => $orderId
            ]);
            
            return response()->json([
                'success' => true,
                'token' => $snap->token,
                'nominal' => $nominal
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Generate QRIS Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function webhook(Request $request)
    {
        \Log::info('Webhook received:', $request->all());
        
        $payload = $request->all();
        
        // Proses webhook jika settlement
        if (isset($payload['transaction_status']) && $payload['transaction_status'] == 'settlement') {
            $pembayaran = Pembayaran::where('midtrans_order_id', $payload['order_id'])->first();
            
            if ($pembayaran && $pembayaran->status == 'belum_lunas') {
                $pembayaran->update([
                    'status' => 'lunas',
                    'paid_at' => now(),
                    'payment_method' => 'qris',
                    'transaction_id' => $payload['transaction_id'] ?? null
                ]);
                
                \Log::info('Payment updated via webhook for ID: ' . $pembayaran->id);
            }
        }
        
        return response()->json(['success' => true]);
    }
}
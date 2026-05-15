<?php
// app/Http/Controllers/Admin/AdminController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    // ============ DASHBOARD ============
    public function dashboard()
    {
        $totalKeluarga = Keluarga::count();
        $totalTagihan = Pembayaran::where('status', 'belum_lunas')->count();
        $totalLunas = Pembayaran::where('status', 'lunas')->count();
        $totalPendapatan = Pembayaran::where('status', 'lunas')->sum('nominal');

        $latestPayments = Pembayaran::with('keluarga')
            ->where('status', 'lunas')
            ->orderBy('paid_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalKeluarga',
            'totalTagihan',
            'totalLunas',
            'totalPendapatan',
            'latestPayments'
        ));
    }

    // ============ CRUD KELUARGA ============
    public function keluargaIndex()
    {
        $keluarga = Keluarga::orderBy('nama_keluarga')->paginate(10);
        return view('admin.keluarga.index', compact('keluarga'));
    }

    public function keluargaCreate()
    {
        return view('admin.keluarga.create');
    }

    public function keluargaStore(Request $request)
    {
        $request->validate([
            'nama_keluarga' => 'required|string|max:100',
            'alamat' => 'nullable|string',
            'no_kk' => 'nullable|string|unique:keluarga',
            'no_telepon' => 'nullable|string|max:15'
        ]);

        Keluarga::create($request->all());

        return redirect()->route('admin.keluarga')
            ->with('success', 'Keluarga berhasil ditambahkan');
    }

    public function keluargaEdit(Keluarga $keluarga)
    {
        return view('admin.keluarga.edit', compact('keluarga'));
    }

    public function keluargaUpdate(Request $request, Keluarga $keluarga)
    {
        $request->validate([
            'nama_keluarga' => 'required|string|max:100',
            'alamat' => 'nullable|string',
            'no_kk' => 'nullable|string|unique:keluarga,no_kk,' . $keluarga->id,
            'no_telepon' => 'nullable|string|max:15'
        ]);

        $keluarga->update($request->all());

        return redirect()->route('admin.keluarga')
            ->with('success', 'Keluarga berhasil diupdate');
    }

    public function keluargaDestroy(Keluarga $keluarga)
    {
        if ($keluarga->pembayaran()->count() > 0) {
            return back()->with('error', 'Keluarga memiliki tagihan, hapus tagihan terlebih dahulu');
        }

        $keluarga->delete();

        return redirect()->route('admin.keluarga')
            ->with('success', 'Keluarga berhasil dihapus');
    }

    // ============ CRUD TAGIHAN ============
    public function tagihanIndex()
    {
        $tagihan = Pembayaran::with('keluarga')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.tagihan.index', compact('tagihan'));
    }

    public function tagihanCreate()
    {
        $keluarga = Keluarga::orderBy('nama_keluarga')->get();
        return view('admin.tagihan.create', compact('keluarga'));
    }

    public function tagihanStore(Request $request)
    {
        $request->validate([
            'keluarga_id' => 'required|exists:keluarga,id',
            'bulan' => 'required|string|max:15',
            'nominal' => 'required|numeric|min:0'
        ]);

        $exists = Pembayaran::where('keluarga_id', $request->keluarga_id)
            ->where('bulan', $request->bulan)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Tagihan untuk bulan ' . $request->bulan . ' sudah ada');
        }

        Pembayaran::create($request->all());

        return redirect()->route('admin.tagihan')
            ->with('success', 'Tagihan berhasil ditambahkan');
    }

    public function tagihanEdit(Pembayaran $pembayaran)
    {
        $keluarga = Keluarga::orderBy('nama_keluarga')->get();
        return view('admin.tagihan.edit', compact('pembayaran', 'keluarga'));
    }

    public function tagihanUpdate(Request $request, Pembayaran $pembayaran)
    {
        $request->validate([
            'keluarga_id' => 'required|exists:keluarga,id',
            'bulan' => 'required|string|max:15',
            'nominal' => 'required|numeric|min:0'
        ]);

        $exists = Pembayaran::where('keluarga_id', $request->keluarga_id)
            ->where('bulan', $request->bulan)
            ->where('id', '!=', $pembayaran->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Tagihan untuk bulan ' . $request->bulan . ' sudah ada');
        }

        $pembayaran->update($request->all());

        return redirect()->route('admin.tagihan')
            ->with('success', 'Tagihan berhasil diupdate');
    }

    public function tagihanDestroy(Pembayaran $pembayaran)
    {
        try {
            $pembayaran->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ============ MANUAL MARK AS PAID ============
    public function markAsPaid(Pembayaran $pembayaran)
    {
        try {
            if ($pembayaran->status === 'lunas') {
                return response()->json(['success' => false, 'message' => 'Tagihan sudah lunas']);
            }

            $pembayaran->update([
                'status' => 'lunas',
                'paid_at' => now(),
                'payment_method' => 'manual',
                'transaction_id' => 'MANUAL-' . time() . '-' . $pembayaran->id
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ============ GENERATE TAGIHAN MASSAL ============
    public function generateBulkForm()
    {
        $keluarga = Keluarga::orderBy('nama_keluarga')->get();
        $bulanList = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $tahunList = range(date('Y') - 1, date('Y') + 1);

        return view('admin.tagihan.bulk', compact('keluarga', 'bulanList', 'tahunList'));
    }

    public function generateBulk(Request $request)
    {
        $request->validate([
            'bulan' => 'required|string|max:15',
            'nominal' => 'required|numeric|min:0',
            'tahun' => 'nullable|integer|min:2020|max:2030',
            'keluarga_ids' => 'nullable|array',
            'keluarga_ids.*' => 'exists:keluarga,id'
        ]);

        $bulan = $request->bulan;
        $nominal = $request->nominal;
        $tahun = $request->tahun ?? date('Y');
        $bulanTahun = $bulan . ' ' . $tahun;

        if ($request->has('keluarga_ids') && !empty($request->keluarga_ids)) {
            $keluargaList = Keluarga::whereIn('id', $request->keluarga_ids)->get();
        } else {
            $keluargaList = Keluarga::all();
        }

        $created = 0;
        $skipped = 0;
        $errors = [];

        foreach ($keluargaList as $keluarga) {
            $exists = Pembayaran::where('keluarga_id', $keluarga->id)
                ->where('bulan', $bulanTahun)
                ->exists();

            if (!$exists) {
                try {
                    Pembayaran::create([
                        'keluarga_id' => $keluarga->id,
                        'bulan' => $bulanTahun,
                        'nominal' => $nominal,
                        'status' => 'belum_lunas'
                    ]);
                    $created++;
                } catch (\Exception $e) {
                    $errors[] = $keluarga->nama_keluarga . ': ' . $e->getMessage();
                    $skipped++;
                }
            } else {
                $skipped++;
            }
        }

        $message = "✅ Berhasil generate $created tagihan untuk periode $bulanTahun.";
        if ($skipped > 0) {
            $message .= " ($skipped sudah ada atau gagal)";
        }

        if (!empty($errors)) {
            $message .= "\n\nError: " . implode(', ', $errors);
        }

        return redirect()->route('admin.tagihan')->with('success', $message);
    }

    // ============ LAPORAN KEUANGAN ============
    public function laporan(Request $request)
    {
        // Filter bulan dan tahun
        $bulan = $request->get('bulan', date('F'));
        $tahun = $request->get('tahun', date('Y'));
        $status = $request->get('status', 'all');
        $bulanTahun = $bulan . ' ' . $tahun;

        // Query pembayaran berdasarkan filter
        $query = Pembayaran::with('keluarga')->where('bulan', $bulanTahun);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $pembayaran = $query->orderBy('keluarga_id')->get();

        // Hitung statistik
        $totalTagihan = $pembayaran->sum('nominal');
        $totalLunas = $pembayaran->where('status', 'lunas')->sum('nominal');
        $totalBelumLunas = $pembayaran->where('status', 'belum_lunas')->sum('nominal');
        $persentase = $totalTagihan > 0 ? ($totalLunas / $totalTagihan) * 100 : 0;

        // Jumlah keluarga
        $totalKeluarga = $pembayaran->count();
        $totalLunasCount = $pembayaran->where('status', 'lunas')->count();
        $totalBelumLunasCount = $pembayaran->where('status', 'belum_lunas')->count();

        // Daftar bulan dan tahun untuk filter
        $bulanList = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $tahunList = range(date('Y') - 2, date('Y') + 1);

        // Data untuk grafik
        $chartData = $this->getChartData($tahun);

        return view('admin.laporan.index', compact(
            'bulan', 'tahun', 'status', 'pembayaran',
            'totalTagihan', 'totalLunas', 'totalBelumLunas', 'persentase',
            'totalKeluarga', 'totalLunasCount', 'totalBelumLunasCount',
            'bulanList', 'tahunList', 'chartData'
        ));
    }

    private function getChartData($tahun)
    {
        $chartData = [];
        $bulanList = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        foreach ($bulanList as $bulan) {
            $bulanTahun = $bulan . ' ' . $tahun;
            $total = Pembayaran::where('bulan', $bulanTahun)
                ->where('status', 'lunas')
                ->sum('nominal');
            $chartData[] = $total;
        }

        return $chartData;
    }

    public function exportPdf(Request $request)
{
    // Filter bulan dan tahun
    $bulan = $request->get('bulan', date('F'));
    $tahun = $request->get('tahun', date('Y'));
    $status = $request->get('status', 'all');
    $bulanTahun = $bulan . ' ' . $tahun;
    
    // Query pembayaran berdasarkan filter
    $query = Pembayaran::with('keluarga')->where('bulan', $bulanTahun);
    
    if ($status !== 'all') {
        $query->where('status', $status);
    }
    
    $pembayaran = $query->orderBy('keluarga_id')->get();
    
    // Hitung statistik
    $totalTagihan = $pembayaran->sum('nominal');
    $totalLunas = $pembayaran->where('status', 'lunas')->sum('nominal');
    $totalBelumLunas = $pembayaran->where('status', 'belum_lunas')->sum('nominal');
    $persentase = $totalTagihan > 0 ? ($totalLunas / $totalTagihan) * 100 : 0;
    
    $data = [
        'bulan' => $bulan,
        'tahun' => $tahun,
        'bulanTahun' => $bulanTahun,
        'status' => $status,
        'pembayaran' => $pembayaran,
        'totalTagihan' => $totalTagihan,
        'totalLunas' => $totalLunas,
        'totalBelumLunas' => $totalBelumLunas,
        'persentase' => $persentase,
        'totalKeluarga' => $pembayaran->count(),
        'totalLunasCount' => $pembayaran->where('status', 'lunas')->count(),
        'totalBelumLunasCount' => $pembayaran->where('status', 'belum_lunas')->count(),
        'tanggal_cetak' => now()->format('d/m/Y H:i:s')
    ];
    
    $pdf = Pdf::loadView('admin.laporan.pdf', $data);
    $pdf->setPaper('A4', 'landscape');
    
    return $pdf->download('laporan_kas_rt_' . $bulanTahun . '.pdf');
}
}
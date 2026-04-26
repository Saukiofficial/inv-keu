<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function cetak(Request $request)
    {
        $dari = $request->dari ?? now()->startOfMonth()->format('Y-m-d');
        $sampai = $request->sampai ?? now()->format('Y-m-d');
        $jenis = $request->jenis ?? null;

        $query = Keuangan::whereBetween('tanggal', [$dari, $sampai])
            ->orderBy('tanggal', 'asc');

        if ($jenis) {
            $query->where('jenis', $jenis);
        }

        $transaksi = $query->get();

        $totalMasuk = $transaksi->where('jenis', 'Masuk')->sum('jumlah');
        $totalKeluar = $transaksi->where('jenis', 'Keluar')->sum('jumlah');
        $saldo = $totalMasuk - $totalKeluar;

        $pdf = Pdf::loadView('laporan.keuangan', compact(
            'transaksi', 'totalMasuk', 'totalKeluar', 'saldo', 'dari', 'sampai', 'jenis'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('laporan-keuangan-' . $dari . '-' . $sampai . '.pdf');
    }
}
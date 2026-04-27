<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use App\Models\Keuangan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    protected function getPeriodeBilling(): array
    {
        $today = Carbon::today();

        // Jika hari ini >= 25, periode mulai tgl 25 bulan ini
        // Jika hari ini < 25, periode mulai tgl 25 bulan lalu
        if ($today->day >= 25) {
            $mulai = Carbon::create($today->year, $today->month, 25)->startOfDay();
        } else {
            $mulai = Carbon::create($today->year, $today->month, 25)->subMonth()->startOfDay();
        }

        $selesai = $mulai->copy()->addMonth()->subDay()->endOfDay(); // tgl 24 bulan depan

        return [$mulai, $selesai];
    }

    protected function getStats(): array
    {
        [$mulai, $selesai] = $this->getPeriodeBilling();

        // Keuangan periode billing
        $totalMasuk = Keuangan::where('jenis', 'Masuk')
            ->whereBetween('tanggal', [$mulai, $selesai])
            ->sum('jumlah');

        $totalKeluar = Keuangan::where('jenis', 'Keluar')
            ->whereBetween('tanggal', [$mulai, $selesai])
            ->sum('jumlah');

        $saldo = $totalMasuk - $totalKeluar;

        // Keuangan all time
        $totalMasukAllTime = Keuangan::where('jenis', 'Masuk')->sum('jumlah');
        $totalKeluarAllTime = Keuangan::where('jenis', 'Keluar')->sum('jumlah');

        // Asset
        $totalAsset = Asset::count();
        $totalNilaiAsset = Asset::sum('harga');
        $assetBaik = Asset::where('kondisi', 'Baik')->count();
        $assetRusak = Asset::where('kondisi', 'Rusak')->count();
        $assetPerbaikan = Asset::where('kondisi', 'Dalam Perbaikan')->count();

        // Trend harian dalam periode billing (7 hari terakhir)
        $trendMasuk = Keuangan::where('jenis', 'Masuk')
            ->whereBetween('tanggal', [$mulai, $selesai])
            ->selectRaw('DATE(tanggal) as date, SUM(jumlah) as total')
            ->groupBy('date')->orderBy('date')
            ->pluck('total')->toArray();

        $trendKeluar = Keuangan::where('jenis', 'Keluar')
            ->whereBetween('tanggal', [$mulai, $selesai])
            ->selectRaw('DATE(tanggal) as date, SUM(jumlah) as total')
            ->groupBy('date')->orderBy('date')
            ->pluck('total')->toArray();

        $trendAsset = Asset::where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('MONTH(created_at) as month, SUM(harga) as total')
            ->groupBy('month')->orderBy('month')
            ->pluck('total')->toArray();

        $labelPeriode = $mulai->format('d M Y') . ' - ' . $selesai->format('d M Y');

        return [
            // === KEUANGAN PERIODE BILLING ===
            Stat::make('💰 Pemasukan Periode Ini', 'Rp ' . number_format($totalMasuk, 0, ',', '.'))
                ->description('Periode: ' . $labelPeriode)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($trendMasuk ?: [0])
                ->color('success'),

            Stat::make('💸 Pengeluaran Periode Ini', 'Rp ' . number_format($totalKeluar, 0, ',', '.'))
                ->description('Periode: ' . $labelPeriode)
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->chart($trendKeluar ?: [0])
                ->color('danger'),

            Stat::make('🏦 Saldo Periode Ini', 'Rp ' . number_format($saldo, 0, ',', '.'))
                ->description($saldo >= 0 ? '✅ Keuangan Sehat' : '⚠️ Perlu Perhatian')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($saldo >= 0 ? 'success' : 'danger'),

            // === ASSET ===
            Stat::make('🖥️ Total Unit Asset', $totalAsset . ' Unit')
                ->description('Baik: ' . $assetBaik . ' | Rusak: ' . $assetRusak . ' | Perbaikan: ' . $assetPerbaikan)
                ->descriptionIcon('heroicon-m-server')
                ->color('info'),

            Stat::make('💎 Total Nilai Asset', 'Rp ' . number_format($totalNilaiAsset, 0, ',', '.'))
                ->description('Akumulasi harga beli semua asset')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart($trendAsset ?: [0])
                ->color('warning'),

            Stat::make('🔧 Asset Bermasalah', ($assetRusak + $assetPerbaikan) . ' Unit')
                ->description('Rusak: ' . $assetRusak . ' | Dalam Perbaikan: ' . $assetPerbaikan)
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color($assetRusak > 0 ? 'danger' : 'success'),
        ];
    }
}
<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use App\Models\Keuangan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalMasuk = Keuangan::where('jenis', 'Masuk')->sum('jumlah');
        $totalKeluar = Keuangan::where('jenis', 'Keluar')->sum('jumlah');
        $saldo = $totalMasuk - $totalKeluar;

        $totalAsset = Asset::count();
        $totalNilaiAsset = Asset::sum('harga');
        $assetBaik = Asset::where('kondisi', 'Baik')->count();
        $assetRusak = Asset::where('kondisi', 'Rusak')->count();
        $assetPerbaikan = Asset::where('kondisi', 'Dalam Perbaikan')->count();

        $masukBulanIni = Keuangan::where('jenis', 'Masuk')
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('jumlah');

        $keluarBulanIni = Keuangan::where('jenis', 'Keluar')
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('jumlah');

        $trendMasuk = Keuangan::where('jenis', 'Masuk')
            ->where('tanggal', '>=', now()->subDays(7))
            ->selectRaw('DATE(tanggal) as date, SUM(jumlah) as total')
            ->groupBy('date')->orderBy('date')
            ->pluck('total')->toArray();

        $trendKeluar = Keuangan::where('jenis', 'Keluar')
            ->where('tanggal', '>=', now()->subDays(7))
            ->selectRaw('DATE(tanggal) as date, SUM(jumlah) as total')
            ->groupBy('date')->orderBy('date')
            ->pluck('total')->toArray();

        $trendAsset = Asset::where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('MONTH(created_at) as month, SUM(harga) as total')
            ->groupBy('month')->orderBy('month')
            ->pluck('total')->toArray();

        return [
            // === KEUANGAN ===
            Stat::make('💰 Total Pemasukan', 'Rp ' . number_format($totalMasuk, 0, ',', '.'))
                ->description('Bulan ini: Rp ' . number_format($masukBulanIni, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($trendMasuk ?: [0])
                ->color('success'),

            Stat::make('💸 Total Pengeluaran', 'Rp ' . number_format($totalKeluar, 0, ',', '.'))
                ->description('Bulan ini: Rp ' . number_format($keluarBulanIni, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->chart($trendKeluar ?: [0])
                ->color('danger'),

            Stat::make('🏦 Saldo Bersih', 'Rp ' . number_format($saldo, 0, ',', '.'))
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
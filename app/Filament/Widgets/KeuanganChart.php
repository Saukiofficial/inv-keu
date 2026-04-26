<?php

namespace App\Filament\Widgets;

use App\Models\Keuangan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class KeuanganChart extends ChartWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 1;

    public function getHeading(): string
    {
        return '📊 Grafik Keuangan 6 Bulan Terakhir';
    }

    protected function getData(): array
    {
        $bulan = [];
        $masuk = [];
        $keluar = [];
        $saldo = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $bulan[] = $date->translatedFormat('M Y');

            $totalMasuk = Keuangan::where('jenis', 'Masuk')
                ->whereMonth('tanggal', $date->month)
                ->whereYear('tanggal', $date->year)
                ->sum('jumlah');

            $totalKeluar = Keuangan::where('jenis', 'Keluar')
                ->whereMonth('tanggal', $date->month)
                ->whereYear('tanggal', $date->year)
                ->sum('jumlah');

            $masuk[] = $totalMasuk;
            $keluar[] = $totalKeluar;
            $saldo[] = $totalMasuk - $totalKeluar;
        }

        return [
            'datasets' => [
                [
                    'type' => 'bar',
                    'label' => 'Uang Masuk',
                    'data' => $masuk,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => '#16a34a',
                    'borderWidth' => 2,
                    'borderRadius' => 6,
                    'borderSkipped' => false,
                ],
                [
                    'type' => 'bar',
                    'label' => 'Uang Keluar',
                    'data' => $keluar,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                    'borderColor' => '#dc2626',
                    'borderWidth' => 2,
                    'borderRadius' => 6,
                    'borderSkipped' => false,
                ],
                [
                    'type' => 'line',
                    'label' => 'Saldo Bersih',
                    'data' => $saldo,
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'borderWidth' => 3,
                    'pointBackgroundColor' => '#6366f1',
                    'pointRadius' => 5,
                    'pointHoverRadius' => 8,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $bulan,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'interaction' => ['mode' => 'index', 'intersect' => false],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'labels' => ['usePointStyle' => true, 'padding' => 16],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) {
                            return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                        }",
                    ],
                ],
            ],
            'scales' => [
                'x' => ['grid' => ['display' => false]],
                'y' => [
                    'grid' => ['color' => 'rgba(156,163,175,0.15)'],
                    'ticks' => [
                        'callback' => "function(value) {
                            if (value >= 1000000) return 'Rp ' + (value/1000000).toFixed(1) + 'Jt';
                            if (value >= 1000) return 'Rp ' + (value/1000).toFixed(0) + 'Rb';
                            return 'Rp ' + value;
                        }",
                    ],
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
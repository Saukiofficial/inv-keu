<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan - Ningrat Net</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }

        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { font-size: 20px; font-weight: bold; color: #1a1a1a; }
        .header h2 { font-size: 14px; color: #555; margin-top: 4px; }
        .header p { font-size: 11px; color: #777; margin-top: 4px; }

        .info-box { display: flex; justify-content: space-between; margin-bottom: 16px; }
        .info-box div { font-size: 11px; }
        .info-box span { font-weight: bold; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        thead tr { background-color: #1e40af; color: white; }
        thead th { padding: 8px; text-align: left; font-size: 11px; }
        tbody tr:nth-child(even) { background-color: #f3f4f6; }
        tbody tr:hover { background-color: #e5e7eb; }
        tbody td { padding: 7px 8px; font-size: 11px; border-bottom: 1px solid #e5e7eb; }

        .badge { padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; }
        .badge-masuk { background-color: #dcfce7; color: #16a34a; }
        .badge-keluar { background-color: #fee2e2; color: #dc2626; }

        .summary { margin-top: 10px; }
        .summary table { width: 350px; margin-left: auto; }
        .summary td { padding: 6px 10px; font-size: 12px; }
        .summary .label { color: #555; }
        .summary .value { font-weight: bold; text-align: right; }
        .summary .total-masuk { color: #16a34a; }
        .summary .total-keluar { color: #dc2626; }
        .summary .saldo-positif { color: #16a34a; font-size: 14px; }
        .summary .saldo-negatif { color: #dc2626; font-size: 14px; }
        .summary tr.saldo-row { border-top: 2px solid #333; }

        .footer { margin-top: 30px; text-align: right; font-size: 11px; color: #777; }
    </style>
</head>
<body>

    <div class="header">
        <h1>NINGRAT NET</h1>
        <h2>Laporan Keuangan</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}
        @if($jenis) | Jenis: {{ $jenis }} @endif</p>
    </div>

    <div class="info-box">
        <div>Dicetak pada: <span>{{ now()->format('d/m/Y H:i') }}</span></div>
        <div>Total Transaksi: <span>{{ $transaksi->count() }} transaksi</span></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Kategori</th>
                <th>Keterangan</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                <td>
                    <span class="badge {{ $item->jenis === 'Masuk' ? 'badge-masuk' : 'badge-keluar' }}">
                        {{ $item->jenis }}
                    </span>
                </td>
                <td>{{ $item->kategori }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
                <td>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center; padding: 20px; color: #999;">
                    Tidak ada transaksi pada periode ini
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td class="label">Total Pemasukan</td>
                <td class="value total-masuk">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Total Pengeluaran</td>
                <td class="value total-keluar">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
            </tr>
            <tr class="saldo-row">
                <td class="label"><strong>Saldo Bersih</strong></td>
                <td class="value {{ $saldo >= 0 ? 'saldo-positif' : 'saldo-negatif' }}">
                    Rp {{ number_format($saldo, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dokumen ini digenerate otomatis oleh sistem Inv-Keu &copy; Ningrat Net
    </div>

</body>
</html>
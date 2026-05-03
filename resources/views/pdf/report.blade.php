<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan Bengkel Las</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 14px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        .summary-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .summary-table th, .summary-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .summary-table th {
            background-color: #f5f5f5;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 8px;
        }
        .data-table th {
            background-color: #f1f5f9;
            text-transform: uppercase;
            font-size: 12px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-success { color: #10b981; }
        .text-danger { color: #ef4444; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Keuangan Bengkel Las</h1>
        <p>
            Periode: 
            @if($filter == 'this_month')
                Bulan Ini ({{ \Carbon\Carbon::now()->translatedFormat('F Y') }})
            @elseif($filter == 'last_month')
                Bulan Lalu ({{ \Carbon\Carbon::now()->subMonth()->translatedFormat('F Y') }})
            @else
                Semua Transaksi
            @endif
        </p>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
    </div>

    <table class="summary-table">
        <tr>
            <th>Total Pemasukan</th>
            <td class="text-right">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
            <th>Total Pengeluaran</th>
            <td class="text-right">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
            <th>Saldo Akhir</th>
            <td class="text-right" style="font-weight: bold;">Rp {{ number_format($balance, 0, ',', '.') }}</td>
        </tr>
    </table>

    <h3 style="margin-bottom: 10px;">Detail Transaksi</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="40%">Keterangan</th>
                <th width="15%">Jenis</th>
                <th width="25%" class="text-right">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $index => $tx)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($tx->date)->translatedFormat('d M Y') }}</td>
                <td>{{ $tx->description }}</td>
                <td class="text-center">{{ $tx->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}</td>
                <td class="text-right {{ $tx->type == 'income' ? 'text-success' : 'text-danger' }}">
                    {{ $tx->type == 'income' ? '+' : '-' }} {{ number_format($tx->amount, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada transaksi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 40px; text-align: right; margin-right: 50px;">
        <p>Hormat Kami,</p>
        <br><br><br>
        <p><strong>Admin Bengkel</strong></p>
    </div>

</body>
</html>

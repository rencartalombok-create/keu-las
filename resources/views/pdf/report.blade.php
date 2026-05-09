<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan Bengkel Las</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #334155;
            line-height: 1.5;
            font-size: 10pt;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 3px solid #1e40af;
            padding-bottom: 20px;
            margin-bottom: 30px;
            width: 100%;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
        }
        .company-name {
            font-size: 22pt;
            font-weight: bold;
            color: #1e40af;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .company-sub {
            font-size: 10pt;
            color: #64748b;
            margin: 0;
        }
        .doc-title {
            font-size: 16pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .doc-period {
            font-size: 10pt;
            color: #64748b;
            margin: 0;
        }
        .summary-panel {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .summary-panel th, .summary-panel td {
            padding: 12px;
            border: 1px solid #cbd5e1;
        }
        .summary-panel th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9pt;
            width: 20%;
        }
        .summary-panel td {
            font-size: 12pt;
            color: #0f172a;
            width: 30%;
        }
        .section-title {
            font-size: 14pt;
            color: #1e40af;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 10px 8px;
        }
        .data-table th {
            background-color: #1e40af;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9pt;
            text-align: center;
        }
        .data-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-success { color: #16a34a; font-weight: bold; }
        .text-danger { color: #dc2626; font-weight: bold; }
        .text-balance { color: #1e40af; font-weight: bold; font-size: 14pt;}
        
        .signature-section {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
        }
        .signature-title {
            color: #475569;
            margin-bottom: 70px;
            font-size: 10pt;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
            font-size: 11pt;
        }
        .signature-role {
            color: #64748b;
            font-size: 9pt;
            margin-top: 5px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 8pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 50%;">
                    <h1 class="company-name">Bengkel Las Galang Bulan</h1>
                    <p class="company-sub">Laporan Keuangan Operasional</p>
                </td>
                <td style="width: 50%; text-align: right;">
                    <h2 class="doc-title">Laporan Keuangan</h2>
                    <p class="doc-period">
                        Periode: 
                        @if($filter == 'this_month')
                            Bulan Ini ({{ \Carbon\Carbon::now()->translatedFormat('F Y') }})
                        @elseif($filter == 'last_month')
                            Bulan Lalu ({{ \Carbon\Carbon::now()->subMonth()->translatedFormat('F Y') }})
                        @else
                            Semua Transaksi
                        @endif
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <table class="summary-panel">
        <tr>
            <th>Total Pemasukan</th>
            <td class="text-right text-success">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
            <th>Total Pengeluaran</th>
            <td class="text-right text-danger">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th colspan="2" style="text-align: right; background-color: #f1f5f9;">Saldo Akhir Periode Ini</th>
            <td colspan="2" class="text-right text-balance">Rp {{ number_format($balance, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="section-title">Detail Transaksi</div>
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
                <td class="text-center">{{ \Carbon\Carbon::parse($tx->date)->translatedFormat('d M Y') }}</td>
                <td>{{ $tx->description }}</td>
                <td class="text-center">
                    <span style="display:inline-block; padding: 2px 6px; border-radius: 4px; font-size: 8pt; font-weight:bold; background-color: {{ $tx->type == 'income' ? '#dcfce7' : '#fee2e2' }}; color: {{ $tx->type == 'income' ? '#16a34a' : '#dc2626' }};">
                        {{ $tx->type == 'income' ? 'PEMASUKAN' : 'PENGELUARAN' }}
                    </span>
                </td>
                <td class="text-right {{ $tx->type == 'income' ? 'text-success' : 'text-danger' }}">
                    {{ $tx->type == 'income' ? '+' : '-' }} {{ number_format($tx->amount, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center" style="padding: 20px; color: #64748b;">Belum ada transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signature-section">
        <tr>
            <td width="60%"></td>
            <td width="40%" class="text-center">
                <div class="signature-title">Hormat Kami,</div>
                <br><br><br><br>
                <div class="signature-name">Admin Keuangan</div>
                <div class="signature-role">Bengkel Las Galang Bulan</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Dicetak secara otomatis oleh Sistem Manajemen Keuangan Bengkel Las Galang Bulan pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB.
    </div>

</body>
</html>

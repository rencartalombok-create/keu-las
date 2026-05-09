<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rencana Anggaran Biaya (RAB)</title>
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
            font-size: 18pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .doc-number {
            font-size: 10pt;
            color: #64748b;
        }
        .info-panel {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .info-panel td {
            padding: 6px 0;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            color: #475569;
            width: 18%;
        }
        .colon {
            width: 2%;
            font-weight: bold;
            color: #475569;
        }
        .value {
            width: 30%;
            color: #0f172a;
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
        .total-row td {
            font-weight: bold;
            background-color: #e2e8f0;
            color: #0f172a;
            font-size: 11pt;
            border-top: 2px solid #94a3b8;
        }
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
                    <p class="company-sub">Layanan Jasa Pengelasan Profesional & Terpercaya</p>
                </td>
                <td style="width: 50%; text-align: right;">
                    <h2 class="doc-title">RAB</h2>
                    <p class="doc-number">No. Dokumen: <strong>RAB-{{ str_pad($rab->id, 4, '0', STR_PAD_LEFT) }}</strong></p>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-panel">
        <tr>
            <td class="label">Nama Proyek</td>
            <td class="colon">:</td>
            <td class="value">{{ $rab->project_name }}</td>
            <td class="label">Tanggal</td>
            <td class="colon">:</td>
            <td class="value">{{ \Carbon\Carbon::parse($rab->date)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Nama Pelanggan</td>
            <td class="colon">:</td>
            <td class="value">{{ $rab->customer_name }}</td>
            <td class="label"></td>
            <td class="colon"></td>
            <td class="value"></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="45%">Deskripsi Pekerjaan / Material</th>
                <th width="10%">Qty</th>
                <th width="20%" class="text-right">Harga Satuan</th>
                <th width="20%" class="text-right">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rab->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL BIAYA KESELURUHAN</td>
                <td class="text-right">Rp {{ number_format($rab->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="signature-section">
        <tr>
            <td width="50%" class="text-center" style="vertical-align: bottom;">
                <div class="signature-title">Disetujui Oleh,</div>
                <br><br><br><br>
                <div class="signature-name">{{ $rab->customer_name }}</div>
                <div class="signature-role">Pelanggan</div>
            </td>
            <td width="50%" class="text-center" style="vertical-align: bottom;">
                <div class="signature-title">Hormat Kami,</div>
                <br><br><br><br>
                <div class="signature-name">Admin / Pemilik</div>
                <div class="signature-role">Bengkel Las Galang Bulan</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Dokumen ini dibuat secara otomatis oleh Sistem Manajemen Keuangan Bengkel Las Galang Bulan pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB.
    </div>

</body>
</html>

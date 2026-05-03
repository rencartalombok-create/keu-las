<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rencana Anggaran Biaya (RAB)</title>
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
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #555;
        }
        .info-table {
            width: 100%;
            margin-bottom: 25px;
        }
        .info-table td {
            padding: 4px;
            vertical-align: top;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row td {
            font-weight: bold;
            background-color: #f8fafc;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Rencana Anggaran Biaya (RAB)</h1>
        <p>Bengkel Las Galang Bulan</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="20%"><strong>Nama Proyek</strong></td>
            <td width="2%">:</td>
            <td width="38%">{{ $rab->project_name }}</td>
            <td width="20%"><strong>Tanggal</strong></td>
            <td width="2%">:</td>
            <td width="18%">{{ \Carbon\Carbon::parse($rab->date)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td><strong>Nama Pelanggan</strong></td>
            <td>:</td>
            <td>{{ $rab->customer_name }}</td>
            <td><strong>No. Dokumen</strong></td>
            <td>:</td>
            <td>RAB-{{ str_pad($rab->id, 4, '0', STR_PAD_LEFT) }}</td>
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
                <td colspan="4" class="text-right">TOTAL BIAYA</td>
                <td class="text-right">Rp {{ number_format($rab->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 50px;">
        <table width="100%">
            <tr>
                <td width="50%" class="text-center">
                    <p>Menyetujui,</p>
                    <p><strong>Pelanggan</strong></p>
                    <br><br><br><br>
                    <p>( {{ $rab->customer_name }} )</p>
                </td>
                <td width="50%" class="text-center">
                    <p>Hormat Kami,</p>
                    <p><strong>Bengkel Las</strong></p>
                    <br><br><br><br>
                    <p>( Admin / Pemilik )</p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

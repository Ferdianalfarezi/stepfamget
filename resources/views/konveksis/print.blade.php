<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Baju — {{ $karyawan->nama }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px 20px;
            min-height: 100vh;
        }

        /* Toolbar — hilang saat print */
        .toolbar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
            width: 100%;
            max-width: 360px;
        }
        .toolbar span { flex: 1; font-size: 13px; color: #64748b; }
        .btn {
            border: none;
            border-radius: 7px;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
        }
        .btn-back  { background: #f1f5f9; color: #475569; }
        .btn-print { background: #0b4614; color: #fff; }

        /* Slip */
        .slip {
            background: #fff;
            width: 320px;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,.10);
            padding: 28px 28px 24px;
        }

        .slip-dept {
            font-size: 36px;
            font-weight: 900;
            color: #111;
            letter-spacing: -1px;
            line-height: 1;
            margin-bottom: 6px;
        }

        .slip-nama {
            font-size: 13px;
            font-weight: 700;
            color: #111;
            margin-bottom: 2px;
        }

        .slip-nik {
            font-size: 11px;
            color: #888;
            margin-bottom: 18px;
        }

        .slip-divider {
            border: none;
            border-top: 1.5px solid #e5e7eb;
            margin-bottom: 16px;
        }

        .slip-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .slip-table tr + tr td { padding-top: 6px; }

        .slip-table td { vertical-align: middle; }

        .slip-table td:nth-child(1) {
            width: 22px;
            font-size: 11px;
            color: #aaa;
        }

        .slip-table td:nth-child(2) {
            font-size: 18px;
            font-weight: 900;
            color: #111;
            width: 60px;
        }

        .slip-table td:nth-child(3) {
            font-size: 12px;
            color: #555;
            padding-left: 4px;
        }

        .slip-qr {
            display: flex;
            justify-content: center;
            padding-top: 4px;
        }

        .slip-qr canvas { width: 90px !important; height: 90px !important; }

        /* Print */
        @media print {
            @page { size: 80mm auto; margin: 6mm; }

            body { background: #fff; padding: 0; }
            .toolbar { display: none !important; }

            .slip {
                width: 100%;
                box-shadow: none;
                border-radius: 0;
                padding: 0;
            }

            .slip-dept { font-size: 28px; }
        }
    </style>
</head>
<body>

<div class="toolbar">
    <span>Slip baju karyawan</span>
    <a href="javascript:history.back()" class="btn btn-back">← Kembali</a>
    <button class="btn btn-print" onclick="window.print()">🖨️ Cetak</button>
</div>

<div class="slip">

    <div class="slip-dept">{{ $karyawan->departemen }}</div>
    <div class="slip-nama">{{ $karyawan->nama }}</div>
    <div class="slip-nik">NIK: {{ $karyawan->nik }}</div>

    <hr class="slip-divider">

    <table class="slip-table">
        @foreach($karyawan->details as $i => $d)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $d->ukuran_kaos ?? '–' }}</td>
            <td>
                @if($d->jenis_kaos === 'Anak')
                    Anak
                @elseif($d->lengan_kaos === 'Lengan Pendek')
                    Pendek
                @elseif($d->lengan_kaos === 'Lengan Panjang')
                    Panjang
                @else
                    –
                @endif
            </td>
        </tr>
        @endforeach
    </table>

    <div class="slip-qr" id="qrcode"></div>

</div>

<script>
new QRCode(document.getElementById('qrcode'), {
    text        : '{{ $karyawan->nik }}',
    width       : 90,
    height      : 90,
    colorDark   : '#000000',
    colorLight  : '#ffffff',
    correctLevel: QRCode.CorrectLevel.M,
});
</script>

</body>
</html>
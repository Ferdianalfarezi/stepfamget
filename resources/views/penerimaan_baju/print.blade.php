<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penerimaan Baju — {{ $dept }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            background: #fff;
            padding: 30px 40px;
        }

        /* ── Header ── */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .header-logo { width: 70px; }
        .header-logo img { width: 100%; }
        .header-center { text-align: center; flex: 1; }
        .header-title {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-dept {
            font-size: 28px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 4px;
        }
        .header-date {
            font-size: 11px;
            color: #555;
            margin-top: 8px;
        }

        .divider {
            border: none;
            border-top: 2px solid #000;
            margin: 10px 0 18px;
        }

        /* ── Section title ── */
        .section-title {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        /* ── Tabel ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        th {
            background: #000;
            color: #fff;
            padding: 7px 10px;
            font-size: 11px;
            font-weight: 700;
            text-align: left;
        }
        td {
            padding: 7px 10px;
            border: 1px solid #ccc;
            font-size: 12px;
            vertical-align: middle;
        }
        tr:nth-child(even) td { background: #f9f9f9; }
        td.bold { font-weight: 700; }

        .empty-row td {
            text-align: center;
            font-style: italic;
            color: #666;
            padding: 14px;
        }

        /* ── Print ── */
        @media print {
            @page { size: A4; margin: 15mm; }
            body { padding: 0; }
            .no-print { display: none !important; }
        }

        /* ── Toolbar ── */
        .toolbar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }
        .btn-print { background: #1e293b; color: #fff; }
        .btn-back  { background: #f1f5f9; color: #475569; }
    </style>
</head>
<body>

{{-- Toolbar --}}
<div class="toolbar no-print">
    <a href="javascript:history.back()" class="btn btn-back">← Kembali</a>
    <button class="btn btn-print" onclick="window.print()">🖨️ Cetak</button>
</div>

{{-- Header --}}
<div class="header">
    <div class="header-logo">
        {{-- Ganti dengan path logo perusahaan --}}
        <img src="{{ asset('images/logostep.png') }}" alt="Logo" onerror="this.style.display='none'">
    </div>
    <div class="header-center">
        <div class="header-title">Laporan Data Baju Family Gathering</div>
        <div class="header-dept">Departemen {{ $dept }}</div>
        <div class="header-date">Tanggal Cetak: {{ now()->format('d M Y - H:i:s') }}</div>
    </div>
    <div class="header-logo" style="text-align:right;">
        <img src="{{ asset('images/logostep.png') }}" alt="STEP" onerror="this.style.display='none'">
    </div>
</div>

<hr class="divider">

{{-- Sudah Diambil --}}
<div class="section-title">Sudah Diambil ({{ $sudah->count() }})</div>
<hr style="border-top:1.5px solid #000;margin-bottom:8px;">
<table>
    <thead>
        <tr>
            <th style="width:40px;">No</th>
            <th style="width:80px;">NIK</th>
            <th>Nama</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sudah as $i => $k)
        @php $sc = $scannedAt[$k->nik] ?? null; @endphp
        <tr>
            <td>{{ $i + 1 }}</td>
            <td class="bold">{{ $k->nik }}</td>
            <td class="bold">{{ $k->nama }}</td>
            <td class="bold">
                Sudah Diambil:
                {{ $sc && $sc->scanned_baju_at ? $sc->scanned_baju_at->format('d/m/Y H:i') : '-' }}
            </td>
        </tr>
        @empty
        <tr class="empty-row">
            <td colspan="4">Belum ada karyawan yang mengambil baju dari departemen ini</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- Belum Diambil --}}
<div class="section-title">Belum Diambil ({{ $belum->count() }})</div>
<hr style="border-top:1.5px solid #000;margin-bottom:8px;">
<table>
    <thead>
        <tr>
            <th style="width:40px;">No</th>
            <th style="width:80px;">NIK</th>
            <th>Nama</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($belum as $i => $k)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td class="bold">{{ $k->nik }}</td>
            <td class="bold">{{ $k->nama }}</td>
            <td style="color:#888;font-style:italic;">Belum mengambil</td>
        </tr>
        @empty
        <tr class="empty-row">
            <td colspan="4"><em>Semua karyawan/karyawati sudah mengambil baju dari departemen ini</em></td>
        </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
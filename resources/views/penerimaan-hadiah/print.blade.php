{{-- resources/views/admin/penerimaan-hadiah/print.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Hadiah - {{ $hadiah->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #000;
            background: white;
        }

        .container {
            width: 96mm;   /* aman di dalam kertas 100x50mm */
            height: 46mm;
            margin: 2mm;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            padding: 3mm;
            position: relative; /* penting supaya absolute anaknya bisa nempel ke container */
        }

        /* ID di tengah */
        .id-value {
            position: relative;
            font-size: 60px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
            width: 100%;
        }

        .id-value span {
            display: inline-block;
        }

        /* Icon pojok */
        .id-value img {
            position: absolute;
            top: 0;   /* pojok atas */
            width: 25px;   /* atur ukuran di sini */
            height: 25px;  /* atur ukuran di sini */
            object-fit: contain;
        }

        .id-value .left-icon {
            left: 0;
            width: 70px;
            height: auto;
            margin-top: 30px;
        }

        .id-value .right-icon {
            right: 0;
            width: 60px;
            height: auto;
        }

        /* Nama barang */
        .name-value {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
        }

        @media print {
            @page {
                size: 100mm 50mm landscape;
                margin: 0;
            }

            body {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Atas: Kode dengan icon pojok -->
        <div class="id-value">
            <img class="left-icon" src="{{ asset('images/logostep2.png') }}" alt="icon kiri">
            <span>{{ $hadiah->id }}</span>
            <img class="right-icon" src="{{ asset('images/30.png') }}" alt="icon kanan">
        </div>

        <!-- Bawah: Nama Barang -->
        <div class="name-value">{{ $hadiah->barang }}</div>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

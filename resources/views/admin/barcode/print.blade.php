<!DOCTYPE html>
<html>
<head>
    <title>Print Barcodes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .print-area {
            padding: 10mm;
            text-align: center;
        }

        .barcode-wrapper {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .barcode {
            border: 1px dashed #000;
            padding: 4mm;
            margin: 5mm;
            width: 70mm;
            height: 40mm;
            box-sizing: border-box;
            text-align: center;
            break-inside: avoid;
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .barcode strong {
            font-size: 12pt;
            margin-bottom: 3mm;
        }

        .barcode svg {
            display: block;
            margin: auto;
            max-width: 100%;
            max-height: 20mm;
        }

        .barcode div:last-child {
            margin-top: 3mm;
            font-size: 10pt;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            .print-area, .print-area * {
                visibility: visible;
            }

            .print-area {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }

            .barcode {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="print-area">
        <h2>Print Barcodes</h2>
        <p>You are printing {{ $quantity }} barcode(s) for <strong>{{ $product->name }}</strong></p>

        <div class="barcode-wrapper">
            @for ($i = 0; $i < $quantity; $i++)
                <div class="barcode">
                    <strong>{{ $product->name }}</strong>
                    <div class="barcode-container">
                        {!! DNS1D::getBarcodeHTML($product->barcode, 'C128', 0.8, 25) !!}

                    </div>
                    <div>{{ $product->barcode }}</div>
                </div>
            @endfor
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.barcode svg').forEach(svg => {
                svg.removeAttribute('width');
                svg.removeAttribute('height');
                svg.style.width = '100%';
                svg.style.height = 'auto';
            });

            window.print();
            setTimeout(() => window.close(), 800);
        });
    </script>
</body>
</html>

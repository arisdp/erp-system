<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print QR Codes - {{ $goodsReceipt->grn_number }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            padding: 20px;
            background: #fff;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
        }

        .label {
            border: 1px dashed #ccc;
            padding: 10px;
            text-align: center;
            background: #fff;
            page-break-inside: avoid;
            height: 220px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .qr-placeholder {
            margin-bottom: 8px;
        }

        .sku {
            font-weight: bold;
            font-size: 14px;
            margin-top: 5px;
        }

        .batch {
            font-size: 10px;
            color: #555;
            margin-top: 2px;
        }

        .company {
            font-size: 9px;
            margin-bottom: 5px;
            text-transform: uppercase;
            border-bottom: 1px solid #eee;
            width: 100%;
            padding-bottom: 2px;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
            }

            .label {
                border: 1px solid #eee;
            }
        }

        .controls {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <div class="controls no-print">
        <div>
            <strong>GRN: {{ $goodsReceipt->grn_number }}</strong> |
            <span>Date: {{ $goodsReceipt->received_date->format('d/m/Y') }}</span>
        </div>
        <button onclick="window.print()"
            style="padding: 8px 20px; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 4px; font-weight: bold;">
            PRINT LABELS
        </button>
    </div>

    <div class="grid">
        @foreach ($goodsReceipt->lines as $line)
            @for ($i = 0; $i < $line->quantity_received; $i++)
                <div class="label">
                    <div class="company">{{ $goodsReceipt->company->name }}</div>
                    <div id="qr-{{ $line->id }}-{{ $i }}" class="qr-placeholder"></div>
                    <div class="sku">{{ $line->product->name }}</div>
                    <div class="batch">{{ $line->batch_number }}</div>
                </div>

                <script>
                    new QRCode(document.getElementById("qr-{{ $line->id }}-{{ $i }}"), {
                        text: "{{ $line->batch_number }}",
                        width: 100,
                        height: 100,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    });
                </script>
            @endfor
        @endforeach
    </div>
</body>

</html>

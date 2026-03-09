<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Balance Sheet — {{ $date }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #1a1a2e;
            background: #fff;
            padding: 30px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #1a1a2e;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .company-name {
            font-size: 20px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #1a1a2e;
        }

        .report-title {
            font-size: 14px;
            font-weight: 600;
            color: #555;
            margin-top: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .report-period {
            font-size: 11px;
            color: #888;
            margin-top: 4px;
        }

        .meta-info {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #999;
            margin-bottom: 20px;
        }

        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .section {
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }

        .section-title {
            background: #1a1a2e;
            color: #fff;
            padding: 10px 12px;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .assets .section-title {
            background: #1565c0;
        }

        .liabilities .section-title {
            background: #c62828;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        tbody td {
            padding: 8px 12px;
            border-bottom: 1px solid #f0f0f0;
        }

        tbody td:last-child {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .subtotal td {
            font-weight: 700;
            background: #fff3e0;
            border-top: 2px solid #ccc;
            font-size: 13px;
        }

        .balance-check {
            margin-top: 20px;
            padding: 12px 16px;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
        }

        .balanced {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }

        .unbalanced {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ef9a9a;
        }

        .controls {
            background: #f0f4ff;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .btn {
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            border: none;
        }

        .btn-print {
            background: #1a1a2e;
            color: #fff;
        }

        .btn-back {
            background: transparent;
            color: #1a1a2e;
            border: 1px solid #1a1a2e;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #bbb;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        @media print {
            .controls {
                display: none;
            }

            body {
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="controls">
        <a href="{{ route('reports.balance-sheet', ['date' => $date]) }}" class="btn btn-back">← Back to Report</a>
        <button onclick="window.print()" class="btn btn-print">🖨️ Print / Save as PDF</button>
    </div>

    <div class="header">
        <div class="company-name">{{ auth()->user()->company->name ?? 'Company Name' }}</div>
        <div class="report-title">Balance Sheet</div>
        <div class="report-period">As of {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</div>
    </div>

    <div class="meta-info">
        <span>Generated: {{ now()->format('d M Y H:i') }}</span>
        <span>Currency: IDR</span>
    </div>

    <div class="two-col">
        <div class="section assets">
            <div class="section-title">Assets</div>
            <table>
                <tbody>
                    <tr>
                        <td>Current Assets / Liquid</td>
                        <td>{{ number_format($assets, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="subtotal">
                        <td>TOTAL ASSETS</td>
                        <td>{{ number_format($assets, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="section liabilities">
            <div class="section-title">Liabilities & Equity</div>
            <table>
                <tbody>
                    <tr>
                        <td>Total Liabilities</td>
                        <td>{{ number_format($liabilities, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Total Equity</td>
                        <td>{{ number_format($equity, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="subtotal">
                        <td>TOTAL LIAB. & EQUITY</td>
                        <td>{{ number_format($liabilities + $equity, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="balance-check {{ $is_balanced ? 'balanced' : 'unbalanced' }}">
        @if ($is_balanced)
            ✅ Balance Sheet is Balanced — Assets = Liabilities + Equity
        @else
            ⚠️ Balance Sheet is NOT Balanced — Difference:
            {{ number_format($assets - ($liabilities + $equity), 0, ',', '.') }}
        @endif
    </div>

    <div class="footer">
        {{ auth()->user()->company->name ?? '' }} • Printed on {{ now()->format('d M Y H:i') }} • Confidential
    </div>
</body>

</html>

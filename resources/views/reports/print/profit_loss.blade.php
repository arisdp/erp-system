<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit & Loss — {{ $startDate }} to {{ $endDate }}</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: #1a1a2e;
            color: #fff;
        }

        thead th {
            padding: 10px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        thead th:last-child {
            text-align: right;
        }

        tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        tbody td {
            padding: 8px 12px;
            border-bottom: 1px solid #eeeeee;
        }

        tbody td:last-child {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        .section-header td {
            background: #e8eaf6;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            color: #1a1a2e;
            padding: 10px 12px;
        }

        .subtotal td {
            font-weight: 600;
            background: #f0f4ff;
            border-top: 1px solid #ccc;
        }

        tfoot tr {
            background: #1a1a2e;
            color: #fff;
        }

        tfoot td {
            padding: 12px;
            font-weight: 700;
            font-size: 14px;
        }

        tfoot td:last-child {
            text-align: right;
        }

        .profit {
            color: #00c853;
        }

        .loss {
            color: #ff5252;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #bbb;
            border-top: 1px solid #eee;
            padding-top: 15px;
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
        <a href="{{ route('reports.profit-loss', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
            class="btn btn-back">← Back to Report</a>
        <div>
            <button onclick="window.print()" class="btn btn-print">🖨️ Print / Save as PDF</button>
        </div>
    </div>

    <div class="header">
        <div class="company-name">{{ auth()->user()->company->name ?? 'Company Name' }}</div>
        <div class="report-title">Profit &amp; Loss Statement</div>
        <div class="report-period">Period: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} to
            {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</div>
    </div>

    <div class="meta-info">
        <span>Generated: {{ now()->format('d M Y H:i') }}</span>
        <span>Currency: IDR</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Amount (IDR)</th>
            </tr>
        </thead>
        <tbody>
            <tr class="section-header">
                <td colspan="2">REVENUE</td>
            </tr>
            <tr>
                <td style="padding-left: 24px;">Sales Revenue / Income</td>
                <td>{{ number_format($revenue, 0, ',', '.') }}</td>
            </tr>
            <tr class="subtotal">
                <td>Total Revenue</td>
                <td>{{ number_format($revenue, 0, ',', '.') }}</td>
            </tr>

            <tr class="section-header">
                <td colspan="2">EXPENSES</td>
            </tr>
            <tr>
                <td style="padding-left: 24px;">Operating Expenses</td>
                <td>{{ number_format($expenses, 0, ',', '.') }}</td>
            </tr>
            <tr class="subtotal">
                <td>Total Expenses</td>
                <td>{{ number_format($expenses, 0, ',', '.') }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td class="{{ $net_profit >= 0 ? 'profit' : 'loss' }}">NET {{ $net_profit >= 0 ? 'PROFIT' : 'LOSS' }}
                </td>
                <td class="{{ $net_profit >= 0 ? 'profit' : 'loss' }}">
                    {{ number_format(abs($net_profit), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        {{ auth()->user()->company->name ?? '' }} • Printed on {{ now()->format('d M Y H:i') }} • Confidential
    </div>
</body>

</html>

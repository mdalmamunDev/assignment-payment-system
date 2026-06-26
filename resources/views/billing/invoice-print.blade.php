<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 13px; color: #222; background: #fff; padding: 30px; }

        .invoice-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; }
        .company-name { font-size: 22px; font-weight: bold; color: #111; }
        .invoice-title { text-align: right; }
        .invoice-title h2 { font-size: 28px; font-weight: bold; color: #333; text-transform: uppercase; }
        .invoice-title p { color: #666; margin-top: 4px; }

        .meta-section { display: flex; justify-content: space-between; margin-bottom: 30px; gap: 20px; }
        .meta-block h4 { font-size: 11px; text-transform: uppercase; color: #999; margin-bottom: 6px; letter-spacing: 0.5px; }
        .meta-block p { margin-bottom: 3px; }

        .status-badge {
            display: inline-block; padding: 3px 10px; border-radius: 20px;
            font-size: 11px; font-weight: bold; text-transform: uppercase;
        }
        .status-draft    { background: #f3f4f6; color: #374151; }
        .status-sent     { background: #dbeafe; color: #1d4ed8; }
        .status-partially_paid { background: #fef3c7; color: #92400e; }
        .status-paid     { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        thead tr { background: #f9fafb; }
        th { text-align: left; padding: 10px 12px; font-size: 11px; text-transform: uppercase; color: #6b7280; border-bottom: 1px solid #e5e7eb; }
        td { padding: 10px 12px; border-bottom: 1px solid #f3f4f6; }

        .totals-section { display: flex; justify-content: flex-end; margin-bottom: 30px; }
        .totals-table { width: 280px; }
        .totals-table tr td { padding: 5px 10px; }
        .totals-table tr td:last-child { text-align: right; }
        .totals-table .total-row td { font-weight: bold; font-size: 14px; border-top: 2px solid #111; padding-top: 8px; }
        .due-row td { color: #dc2626; font-weight: bold; }

        .payments-section h3 { font-size: 13px; font-weight: bold; margin-bottom: 10px; color: #333; }

        .footer { margin-top: 40px; border-top: 1px solid #e5e7eb; padding-top: 16px; text-align: center; color: #9ca3af; font-size: 11px; }

        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            @page { margin: 15mm; }
        }
    </style>
</head>
<body>

    <!-- Print Button (hidden on print) -->
    <div class="no-print" style="text-align:right; margin-bottom: 20px;">
        <button onclick="window.print()"
            style="background:#1d4ed8; color:#fff; border:none; padding:8px 20px; border-radius:6px; cursor:pointer; font-size:13px;">
            🖨 Print Invoice
        </button>
        <button onclick="window.history.back()"
            style="background:#6b7280; color:#fff; border:none; padding:8px 20px; border-radius:6px; cursor:pointer; font-size:13px; margin-left:8px;">
            ← Back
        </button>
    </div>

    <!-- Header -->
    <div class="invoice-header">
        <div>
            <div class="company-name">FunTan</div>
            <p style="color:#666; margin-top:4px;">Billing & Project Management</p>
        </div>
        <div class="invoice-title">
            <h2>Invoice</h2>
            <p><strong>{{ $invoice->invoice_number }}</strong></p>
            <p style="margin-top:6px;">
                <span class="status-badge status-{{ $invoice->status }}">
                    {{ $invoice->status_label }}
                </span>
            </p>
        </div>
    </div>

    <!-- Meta -->
    <div class="meta-section">
        <div class="meta-block">
            <h4>Bill To</h4>
            <p><strong>{{ $invoice->project->customer->name }}</strong></p>
            @if($invoice->project->customer->company_name)
                <p>{{ $invoice->project->customer->company_name }}</p>
            @endif
            <p>{{ $invoice->project->customer->email }}</p>
            @if($invoice->project->customer->phone)
                <p>{{ $invoice->project->customer->phone }}</p>
            @endif
            @if($invoice->project->customer->address)
                <p>{{ $invoice->project->customer->address }}</p>
            @endif
        </div>
        <div class="meta-block">
            <h4>Project</h4>
            <p><strong>{{ $invoice->project->project_name }}</strong></p>
            <p style="color:#666">{{ $invoice->project->project_code }}</p>
        </div>
        <div class="meta-block" style="text-align:right">
            <h4>Invoice Details</h4>
            <p><strong>Date:</strong> {{ $invoice->invoice_date?->format('d M Y') }}</p>
            @if($invoice->due_date)
                <p><strong>Due Date:</strong> {{ $invoice->due_date->format('d M Y') }}</p>
            @endif
        </div>
    </div>

    <!-- Amount Breakdown -->
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align:right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Service / Invoice Amount</td>
                <td style="text-align:right">{{ number_format($invoice->invoice_amount, 2) }}</td>
            </tr>
            @if($invoice->tax_amount > 0)
            <tr>
                <td>Tax / VAT</td>
                <td style="text-align:right">+ {{ number_format($invoice->tax_amount, 2) }}</td>
            </tr>
            @endif
            @if($invoice->discount_amount > 0)
            <tr>
                <td>Discount</td>
                <td style="text-align:right">- {{ number_format($invoice->discount_amount, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals-section">
        <table class="totals-table">
            <tr>
                <td>Final Amount</td>
                <td><strong>{{ number_format($invoice->final_amount, 2) }}</strong></td>
            </tr>
            <tr>
                <td>Paid Amount</td>
                <td>{{ number_format($invoice->paid_amount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Total Amount</td>
                <td>{{ number_format($invoice->final_amount, 2) }}</td>
            </tr>
            @if($invoice->due_amount > 0)
            <tr class="due-row">
                <td>Due Amount</td>
                <td>{{ number_format($invoice->due_amount, 2) }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Payment History -->
    @if($invoice->payments->count() > 0)
    <div class="payments-section">
        <h3>Payment History</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Method</th>
                    <th>Reference</th>
                    <th style="text-align:right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->payments as $i => $payment)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $payment->payment_date?->format('d M Y') }}</td>
                    <td>{{ $payment->method_label }}</td>
                    <td>{{ $payment->reference_no ?? '—' }}</td>
                    <td style="text-align:right">{{ number_format($payment->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Thank you for your business. This is a computer-generated invoice.</p>
        <p style="margin-top:4px;">Generated on {{ now()->format('d M Y, h:i A') }}</p>
    </div>

</body>
</html>
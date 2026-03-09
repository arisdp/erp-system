<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
        }

        .header {
            background: #1a1a2e;
            color: #fff;
            padding: 20px;
            text-align: center;
            border-radius: 6px 6px 0 0;
        }

        .content {
            padding: 20px;
        }

        .footer {
            text-align: center;
            font-size: 11px;
            color: #777;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin-top: 20px;
        }

        .btn-approve {
            background: #28a745;
        }

        .btn-reject {
            background: #dc3545;
            margin-left: 10px;
        }

        .details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }

        table {
            width: 100%;
        }

        td {
            padding: 5px 0;
        }

        .label {
            font-weight: bold;
            color: #555;
            width: 120px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>ERP SYSTEM</h2>
        <p>Approval Request Notification</p>
    </div>
    <div class="content">
        <p>Hello,</p>
        <p>A new approval request has been submitted by <strong>{{ $approval->requestedBy->name }}</strong>.</p>

        <div class="details">
            <table>
                <tr>
                    <td class="label">Document Type:</td>
                    <td>{{ str_replace('App\\Models\\', '', $approval->approvable_type) }}</td>
                </tr>
                <tr>
                    <td class="label">Document No:</td>
                    <td><strong>{{ $document->so_number ?? ($document->po_number ?? ($document->number ?? 'N/A')) }}</strong>
                    </td>
                </tr>
                <tr>
                    <td class="label">Date:</td>
                    <td>{{ $approval->created_at->format('d M Y H:i') }}</td>
                </tr>
                <tr>
                    <td class="label">Status:</td>
                    <td><span style="color: #007bff;">{{ $approval->status }}</span></td>
                </tr>
            </table>
        </div>

        <p>Please review this request and take action:</p>

        <div style="text-align: center;">
            <a href="{{ url('/approvals/approve/' . $approval->id) }}" class="btn btn-approve">Approve</a>
            <a href="{{ url('/approvals/reject/' . $approval->id) }}" class="btn btn-reject">Reject</a>
        </div>

        <p style="margin-top: 20px;">Or log in to the system to view full details.</p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} ERP SaaS System. This is an automated email, please do not reply.
    </div>
</body>

</html>

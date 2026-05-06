<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Georgia', serif; background: #f9f9f7; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #1e293b; padding: 40px; text-align: center; }
        .header h1 { color: white; font-size: 28px; margin: 0; letter-spacing: 2px; }
        .header p { color: #A89070; margin: 8px 0 0; font-size: 14px; letter-spacing: 1px; }
        .body { padding: 40px; }
        .greeting { font-size: 18px; color: #1e293b; margin-bottom: 16px; }
        .message { color: #64748b; line-height: 1.7; margin-bottom: 32px; white-space: pre-wrap; }
        .details-card { background: #f8f7f5; border-radius: 10px; padding: 24px; margin-bottom: 32px; }
        .details-card h3 { color: #1e293b; font-size: 14px; text-transform: uppercase; letter-spacing: 2px; margin: 0 0 16px; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e2e8f0; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #94a3b8; font-size: 13px; }
        .detail-value { color: #1e293b; font-size: 13px; font-weight: 600; }
        .footer { background: #f8f7f5; padding: 24px 40px; text-align: center; }
        .footer p { color: #94a3b8; font-size: 12px; margin: 4px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>DwellCasa</h1>
            <p>{{ strtoupper($inquiry->location?->name ?? 'DwellCasa') }}</p>
        </div>
        <div class="body">
            <p class="greeting">Dear {{ $inquiry->name ?? 'Valued Guest' }},</p>
            <p class="message">{{ $replyMessage }}</p>
            
            <div class="details-card">
                <h3>Your Original Inquiry Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Name: </span>
                    <span class="detail-value">{{ $inquiry->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Phone: </span>
                    <span class="detail-value">{{ $inquiry->phone ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
        <div class="footer">
            <p>{{ $inquiry->location?->name ?? 'DwellCasa' }}</p>
            <p>If you have any further questions, please reply directly to this email.</p>
        </div>
    </div>
</body>
</html>

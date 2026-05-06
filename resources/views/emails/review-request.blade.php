<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Georgia, serif;
            background: #f9f9f7;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .header {
            background: #1e293b;
            padding: 40px;
            text-align: center;
        }

        .header h1 {
            color: white;
            font-size: 28px;
            margin: 0;
            letter-spacing: 2px;
        }

        .header p {
            color: #A89070;
            margin: 8px 0 0;
            font-size: 14px;
        }

        .body {
            padding: 40px;
        }

        .greeting {
            font-size: 18px;
            color: #1e293b;
            margin-bottom: 16px;
        }

        .message {
            color: #64748b;
            line-height: 1.8;
            margin-bottom: 32px;
        }

        .stars {
            text-align: center;
            font-size: 32px;
            margin-bottom: 32px;
            letter-spacing: 8px;
        }

        .cta {
            text-align: center;
            margin-bottom: 32px;
        }

        .cta a {
            background: #A89070;
            color: white;
            padding: 16px 40px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .details {
            background: #f8f7f5;
            border-radius: 10px;
            padding: 20px 24px;
            margin-bottom: 32px;
        }

        .details p {
            margin: 6px 0;
            color: #64748b;
            font-size: 13px;
        }

        .details strong {
            color: #1e293b;
        }

        .footer {
            background: #f8f7f5;
            padding: 24px 40px;
            text-align: center;
        }

        .footer p {
            color: #94a3b8;
            font-size: 12px;
            margin: 4px 0;
        }

        .note {
            color: #94a3b8;
            font-size: 12px;
            text-align: center;
            margin-top: 16px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>DwellCasa</h1>
            <p>{{ strtoupper($booking->location?->name ?? 'DwellCasa') }}</p>
        </div>
        <div class="body">
            <p class="greeting">Dear {{ $booking->guest->full_name }},</p>
            <p class="message">
                Thank you for choosing DwellCasa. We hope your stay in the
                <strong>{{ $booking->roomType->name }}</strong> was everything you hoped for.
                Your feedback means the world to us — it takes less than a minute and helps us
                continue improving for future guests.
            </p>

            <div class="details">
                <p><strong>Room:</strong> {{ $booking->roomType->name }}</p>
                <p><strong>Check-in:</strong> {{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</p>
                <p><strong>Check-out:</strong> {{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }}</p>
                <p><strong>Booking Ref:</strong> {{ $booking->booking_ref }}</p>
            </div>

            <div class="cta">
                <a href="{{ url('/review/' . $token) }}">Share Your Experience</a>
            </div>

            <p class="note">This link is unique to your stay and can only be used once.</p>
        </div>
        <div class="footer">
            <p>Dwellcasa, {{ $booking->location?->name ?? 'DwellCasa' }}</p>
            <p>If you did not stay with us recently, please ignore this email.</p>
        </div>
    </div>
</body>

</html>
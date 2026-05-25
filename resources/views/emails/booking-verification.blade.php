<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Georgia', serif;
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
            letter-spacing: 1px;
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
            line-height: 1.7;
            margin-bottom: 24px;
        }

        .details-card {
            background: #f8f7f5;
            border-radius: 10px;
            padding: 24px;
            margin-bottom: 32px;
        }

        .details-card h3 {
            color: #1e293b;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 0 0 16px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #94a3b8;
            font-size: 13px;
        }

        .detail-value {
            color: #1e293b;
            font-size: 13px;
            font-weight: 600;
        }

        .cta-wrap {
            text-align: center;
            margin-bottom: 28px;
        }

        .cta-button {
            display: inline-block;
            background: #1e293b;
            color: white;
            text-decoration: none;
            padding: 14px 36px;
            border-radius: 8px;
            font-size: 15px;
            letter-spacing: 1px;
        }

        .expiry-note {
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
            margin-bottom: 24px;
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
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>DwellCasa</h1>
            <p>{{ strtoupper($locationName) }}</p>
        </div>
        <div class="body">
            <p class="greeting">Dear {{ $bookingData['guest_name'] ?? 'Valued Guest' }},</p>
            <p class="message">
                We received your booking request. To confirm your request and reserve your spot,
                please verify your email address by clicking the button below.
            </p>
            <div class="details-card">
                <h3>Your Request Summary</h3>
                <div class="detail-row">
                    <span class="detail-label">Reference No.</span>
                    <span class="detail-value">{{ $bookingData['booking_ref'] }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Room Type</span>
                    <span class="detail-value">{{ $bookingData['room_type_name'] ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Check-in</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($bookingData['check_in_date'])->format('M d, Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Check-out</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($bookingData['check_out_date'])->format('M d, Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Guests</span>
                    <span class="detail-value">{{ $bookingData['num_guests'] }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Estimated Total</span>
                    <span class="detail-value">Rs. {{ number_format($bookingData['total_amount'], 0) }}</span>
                </div>
            </div>
            <div class="cta-wrap">
                <a href="{{ $verifyUrl }}" class="cta-button">Confirm My Booking Request</a>
            </div>
            <p class="expiry-note">This link expires in 30 minutes. If you did not make this request, you can ignore this email.</p>
            <p class="message">
                Once confirmed, our team will review your request and contact you via email or phone to finalize your booking.
            </p>
        </div>
        <div class="footer">
            <p>{{ $locationName }}</p>
            <p>This is an automated message, please do not reply directly to this email.</p>
        </div>
    </div>
</body>

</html>

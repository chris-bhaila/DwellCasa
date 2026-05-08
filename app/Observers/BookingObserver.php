<?php

namespace App\Observers;

use App\Models\Booking;

class BookingObserver
{
    public function saving(Booking $booking): void
    {
        $net     = ($booking->total_amount ?? 0) - ($booking->discount ?? 0);
        $extra   = optional($booking->checkOut)->extra_charges ?? 0;
        $paid    = $booking->amount_paid ?? 0;
        $deposit = $booking->deposit_amount ?? 0;

        if ($booking->payment_status === 'refunded') return;

        if ($paid == 0 && $deposit == 0) {
            $booking->payment_status = 'unpaid';
        } elseif ($deposit > 0 && $paid == 0) {
            $booking->payment_status = 'deposit_paid';
        } elseif (($paid + $deposit) >= ($net + $extra)) {
            $booking->payment_status = 'fully_paid';
        } else {
            $booking->payment_status = 'partially_paid';
        }
    }
}

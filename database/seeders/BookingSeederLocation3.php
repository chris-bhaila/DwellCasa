<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Guest;
use Illuminate\Database\Seeder;

// Location 3 room types and rooms:
//   Type 6 - Studio Room: Room 9 (101), Room 10 (102), Room 11 (201)
//   Type 7 - Family Room: Room 12 (301)

class BookingSeederLocation3 extends Seeder
{
    public function run(): void
    {
        $locationId = 3;

        $guests = $this->createGuests($locationId);
        $this->createBookings($locationId, $guests);
    }

    private function createGuests(int $locationId): array
    {
        return [
            Guest::create([
                'location_id' => $locationId,
                'full_name'   => 'Marco Rossi',
                'email'       => 'marco.rossi@email.com',
                'phone'       => '+39-02-1234-5678',
                'nationality' => 'Italian',
                'id_type'     => 'passport',
                'id_number'   => 'IT-MR8008',
                'address'     => 'Rome, Italy',
            ]),
            Guest::create([
                'location_id' => $locationId,
                'full_name'   => 'Sita Maharjan',
                'email'       => 'sita.maharjan@email.com',
                'phone'       => '+977-9851234504',
                'nationality' => 'Nepali',
                'id_type'     => 'citizenship',
                'id_number'   => 'CIT-D4004',
                'address'     => 'Bhaktapur, Nepal',
            ]),
            Guest::create([
                'location_id' => $locationId,
                'full_name'   => 'Emma Thompson',
                'email'       => 'emma.thompson@email.com',
                'phone'       => '+61-4-1234-5678',
                'nationality' => 'Australian',
                'id_type'     => 'passport',
                'id_number'   => 'AU-ET9009',
                'address'     => 'Sydney, Australia',
            ]),
            Guest::create([
                'location_id' => $locationId,
                'full_name'   => 'Raj Kumar Singh',
                'email'       => 'raj.singh@email.com',
                'phone'       => '+91-98765-43210',
                'nationality' => 'Indian',
                'id_type'     => 'passport',
                'id_number'   => 'IN-RK0010',
                'address'     => 'Mumbai, India',
            ]),
        ];
    }

    private function createBookings(int $locationId, array $guests): void
    {
        $bookings = [
            // Room 9 (101) — Studio Room — past stay
            [
                'booking_ref'    => 'DWELL-L3-0001',
                'guest_id'       => $guests[0]->id,
                'room_type_id'   => 6,
                'room_id'        => 9,
                'location_id'    => $locationId,
                'check_in_date'  => now()->subDays(50)->toDateString(),
                'check_out_date' => now()->subDays(44)->toDateString(),
                'num_guests'     => 1,
                'stay_type'      => 'short_term',
                'rate_per_night' => 3500.00,
                'total_amount'   => 21000.00,
                'deposit_amount' => 7000.00,
                'amount_paid'    => 21000.00,
                'status'         => 'checked_out',
                'payment_status' => 'fully_paid',
                'checked_in_at'  => now()->subDays(50)->setTime(14, 0),
                'checked_out_at' => now()->subDays(44)->setTime(11, 0),
            ],
            // Room 9 (101) — Studio Room — currently checked in
            [
                'booking_ref'    => 'DWELL-L3-0002',
                'guest_id'       => $guests[1]->id,
                'room_type_id'   => 6,
                'room_id'        => 9,
                'location_id'    => $locationId,
                'check_in_date'  => now()->subDays(7)->toDateString(),
                'check_out_date' => now()->addDays(3)->toDateString(),
                'num_guests'     => 1,
                'stay_type'      => 'short_term',
                'rate_per_night' => 3500.00,
                'total_amount'   => 35000.00,
                'deposit_amount' => 7000.00,
                'amount_paid'    => 24500.00,
                'status'         => 'checked_in',
                'payment_status' => 'partially_paid',
                'checked_in_at'  => now()->subDays(7)->setTime(14, 0),
                'checked_out_at' => now()->addDays(3)->setTime(11, 0),
            ],
            // Room 10 (102) — Studio Room — currently checked in (long term)
            [
                'booking_ref'    => 'DWELL-L3-0003',
                'guest_id'       => $guests[2]->id,
                'room_type_id'   => 6,
                'room_id'        => 10,
                'location_id'    => $locationId,
                'check_in_date'  => now()->subDays(3)->toDateString(),
                'check_out_date' => now()->addDays(27)->toDateString(),
                'num_guests'     => 2,
                'stay_type'      => 'long_term',
                'rate_per_month' => 70000.00,
                'total_amount'   => 70000.00,
                'deposit_amount' => 14000.00,
                'amount_paid'    => 14000.00,
                'status'         => 'checked_in',
                'payment_status' => 'deposit_paid',
                'checked_in_at'  => now()->subDays(3)->setTime(14, 0),
                'checked_out_at' => now()->addDays(27)->setTime(11, 0),
            ],
            // Room 11 (201) — Studio Room — past cancelled
            [
                'booking_ref'    => 'DWELL-L3-0004',
                'guest_id'       => $guests[3]->id,
                'room_type_id'   => 6,
                'room_id'        => 11,
                'location_id'    => $locationId,
                'check_in_date'  => now()->subDays(20)->toDateString(),
                'check_out_date' => now()->subDays(17)->toDateString(),
                'num_guests'     => 1,
                'stay_type'      => 'short_term',
                'rate_per_night' => 3500.00,
                'total_amount'   => 10500.00,
                'deposit_amount' => 7000.00,
                'amount_paid'    => 7000.00,
                'status'         => 'cancelled',
                'payment_status' => 'refunded',
                'refund_amount'  => 7000.00,
                'refunded_at'    => now()->subDays(19),
            ],
            // Room 11 (201) — Studio Room — future confirmed
            [
                'booking_ref'    => 'DWELL-L3-0005',
                'guest_id'       => $guests[0]->id,
                'room_type_id'   => 6,
                'room_id'        => 11,
                'location_id'    => $locationId,
                'check_in_date'  => now()->addDays(7)->toDateString(),
                'check_out_date' => now()->addDays(14)->toDateString(),
                'num_guests'     => 2,
                'stay_type'      => 'short_term',
                'rate_per_night' => 3500.00,
                'total_amount'   => 24500.00,
                'deposit_amount' => 7000.00,
                'amount_paid'    => 7000.00,
                'status'         => 'confirmed',
                'payment_status' => 'deposit_paid',
            ],
            // Room 12 (301) — Family Room — past stay
            [
                'booking_ref'    => 'DWELL-L3-0006',
                'guest_id'       => $guests[2]->id,
                'room_type_id'   => 7,
                'room_id'        => 12,
                'location_id'    => $locationId,
                'check_in_date'  => now()->subDays(22)->toDateString(),
                'check_out_date' => now()->subDays(20)->toDateString(),
                'num_guests'     => 3,
                'stay_type'      => 'short_term',
                'rate_per_night' => 9000.00,
                'total_amount'   => 18000.00,
                'deposit_amount' => 9000.00,
                'amount_paid'    => 18000.00,
                'status'         => 'checked_out',
                'payment_status' => 'fully_paid',
                'checked_in_at'  => now()->subDays(22)->setTime(14, 0),
                'checked_out_at' => now()->subDays(20)->setTime(11, 0),
            ],
            // Room 12 (301) — Family Room — future confirmed
            [
                'booking_ref'    => 'DWELL-L3-0007',
                'guest_id'       => $guests[3]->id,
                'room_type_id'   => 7,
                'room_id'        => 12,
                'location_id'    => $locationId,
                'check_in_date'  => now()->addDays(30)->toDateString(),
                'check_out_date' => now()->addDays(34)->toDateString(),
                'num_guests'     => 4,
                'stay_type'      => 'short_term',
                'rate_per_night' => 9000.00,
                'total_amount'   => 36000.00,
                'deposit_amount' => 9000.00,
                'amount_paid'    => 0.00,
                'status'         => 'pending',
                'payment_status' => 'unpaid',
            ],
        ];

        foreach ($bookings as $data) {
            Booking::create($data);
        }
    }
}

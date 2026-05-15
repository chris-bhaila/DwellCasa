<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Guest;
use Illuminate\Database\Seeder;

// Location 1 room types and rooms:
//   Type 1 - Luxury Suite:  Room 1 (101), Room 2 (102)
//   Type 2 - Deluxe King:   Room 3 (201), Room 4 (202)
//   Type 3 - Classic Suite: Room 5 (501)

class BookingSeederLocation1 extends Seeder
{
    public function run(): void
    {
        $locationId = 1;

        $guests = $this->createGuests($locationId);
        $this->createBookings($locationId, $guests);
    }

    private function createGuests(int $locationId): array
    {
        return [
            Guest::create([
                'location_id' => $locationId,
                'full_name'   => 'Aarav Sharma',
                'email'       => 'aarav.sharma@email.com',
                'phone'       => '+977-9801234501',
                'nationality' => 'Nepali',
                'id_type'     => 'passport',
                'id_number'   => 'NP-A1001',
                'address'     => 'Kathmandu, Nepal',
            ]),
            Guest::create([
                'location_id' => $locationId,
                'full_name'   => 'Priya Thapa',
                'email'       => 'priya.thapa@email.com',
                'phone'       => '+977-9801234502',
                'nationality' => 'Nepali',
                'id_type'     => 'citizenship',
                'id_number'   => 'CIT-B2002',
                'address'     => 'Pokhara, Nepal',
            ]),
            Guest::create([
                'location_id' => $locationId,
                'full_name'   => 'James Wilson',
                'email'       => 'james.wilson@email.com',
                'phone'       => '+1-555-0101',
                'nationality' => 'American',
                'id_type'     => 'passport',
                'id_number'   => 'US-JW3003',
                'address'     => 'New York, USA',
            ]),
            Guest::create([
                'location_id' => $locationId,
                'full_name'   => 'Sofia Patel',
                'email'       => 'sofia.patel@email.com',
                'phone'       => '+44-7700-900001',
                'nationality' => 'British',
                'id_type'     => 'passport',
                'id_number'   => 'UK-SP4004',
                'address'     => 'London, UK',
            ]),
        ];
    }

    private function createBookings(int $locationId, array $guests): void
    {
        $bookings = [
            // Room 1 (101) — Luxury Suite — past stay
            [
                'booking_ref'    => 'DWELL-L1-0001',
                'guest_id'       => $guests[0]->id,
                'room_type_id'   => 1,
                'room_id'        => 1,
                'location_id'    => $locationId,
                'check_in_date'  => now()->subDays(60)->toDateString(),
                'check_out_date' => now()->subDays(53)->toDateString(),
                'num_guests'     => 2,
                'stay_type'      => 'short_term',
                'rate_per_night' => 8000.00,
                'total_amount'   => 56000.00,
                'deposit_amount' => 16000.00,
                'amount_paid'    => 56000.00,
                'status'         => 'checked_out',
                'payment_status' => 'fully_paid',
                'checked_in_at'  => now()->subDays(60)->setTime(14, 0),
                'checked_out_at' => now()->subDays(53)->setTime(11, 0),
            ],
            // Room 1 (101) — Luxury Suite — currently checked in
            [
                'booking_ref'    => 'DWELL-L1-0002',
                'guest_id'       => $guests[1]->id,
                'room_type_id'   => 1,
                'room_id'        => 1,
                'location_id'    => $locationId,
                'check_in_date'  => now()->subDays(4)->toDateString(),
                'check_out_date' => now()->addDays(10)->toDateString(),
                'num_guests'     => 1,
                'stay_type'      => 'short_term',
                'rate_per_night' => 8000.00,
                'total_amount'   => 112000.00,
                'deposit_amount' => 16000.00,
                'amount_paid'    => 32000.00,
                'status'         => 'checked_in',
                'payment_status' => 'partially_paid',
                'checked_in_at'  => now()->subDays(4)->setTime(14, 0),
                'checked_out_at' => now()->addDays(10)->setTime(11, 0),
            ],
            // Room 2 (102) — Luxury Suite — future confirmed
            [
                'booking_ref'    => 'DWELL-L1-0003',
                'guest_id'       => $guests[2]->id,
                'room_type_id'   => 1,
                'room_id'        => 2,
                'location_id'    => $locationId,
                'check_in_date'  => now()->addDays(5)->toDateString(),
                'check_out_date' => now()->addDays(12)->toDateString(),
                'num_guests'     => 2,
                'stay_type'      => 'short_term',
                'rate_per_night' => 8000.00,
                'total_amount'   => 56000.00,
                'deposit_amount' => 16000.00,
                'amount_paid'    => 16000.00,
                'status'         => 'confirmed',
                'payment_status' => 'deposit_paid',
            ],
            // Room 3 (201) — Deluxe King — past stay
            [
                'booking_ref'    => 'DWELL-L1-0004',
                'guest_id'       => $guests[3]->id,
                'room_type_id'   => 2,
                'room_id'        => 3,
                'location_id'    => $locationId,
                'check_in_date'  => now()->subDays(30)->toDateString(),
                'check_out_date' => now()->subDays(23)->toDateString(),
                'num_guests'     => 2,
                'stay_type'      => 'short_term',
                'rate_per_night' => 6000.00,
                'total_amount'   => 42000.00,
                'deposit_amount' => 12000.00,
                'amount_paid'    => 42000.00,
                'status'         => 'checked_out',
                'payment_status' => 'fully_paid',
                'checked_in_at'  => now()->subDays(30)->setTime(14, 0),
                'checked_out_at' => now()->subDays(23)->setTime(11, 0),
            ],
            // Room 3 (201) — Deluxe King — currently checked in (long term)
            [
                'booking_ref'    => 'DWELL-L1-0005',
                'guest_id'       => $guests[0]->id,
                'room_type_id'   => 2,
                'room_id'        => 3,
                'location_id'    => $locationId,
                'check_in_date'  => now()->subDays(3)->toDateString(),
                'check_out_date' => now()->addDays(27)->toDateString(),
                'num_guests'     => 1,
                'stay_type'      => 'long_term',
                'rate_per_month' => 120000.00,
                'total_amount'   => 120000.00,
                'deposit_amount' => 24000.00,
                'amount_paid'    => 24000.00,
                'status'         => 'checked_in',
                'payment_status' => 'deposit_paid',
                'checked_in_at'  => now()->subDays(3)->setTime(14, 0),
                'checked_out_at' => now()->addDays(27)->setTime(11, 0),
            ],
            // Room 4 (202) — Deluxe King — future pending
            [
                'booking_ref'    => 'DWELL-L1-0006',
                'guest_id'       => $guests[1]->id,
                'room_type_id'   => 2,
                'room_id'        => 4,
                'location_id'    => $locationId,
                'check_in_date'  => now()->addDays(15)->toDateString(),
                'check_out_date' => now()->addDays(18)->toDateString(),
                'num_guests'     => 2,
                'stay_type'      => 'short_term',
                'rate_per_night' => 6000.00,
                'total_amount'   => 18000.00,
                'deposit_amount' => 12000.00,
                'amount_paid'    => 0.00,
                'status'         => 'pending',
                'payment_status' => 'unpaid',
            ],
            // Room 5 (501) — Classic Suite — future confirmed
            [
                'booking_ref'    => 'DWELL-L1-0007',
                'guest_id'       => $guests[3]->id,
                'room_type_id'   => 3,
                'room_id'        => 5,
                'location_id'    => $locationId,
                'check_in_date'  => now()->addDays(2)->toDateString(),
                'check_out_date' => now()->addDays(6)->toDateString(),
                'num_guests'     => 2,
                'stay_type'      => 'short_term',
                'rate_per_night' => 5000.00,
                'total_amount'   => 20000.00,
                'deposit_amount' => 10000.00,
                'amount_paid'    => 10000.00,
                'status'         => 'confirmed',
                'payment_status' => 'deposit_paid',
            ],
        ];

        foreach ($bookings as $data) {
            Booking::create($data);
        }
    }
}

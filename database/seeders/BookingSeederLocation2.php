<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Guest;
use Illuminate\Database\Seeder;

// Location 2 room types and rooms:
//   Type 4 - Junior Suite:    Room 6 (101), Room 7 (301)
//   Type 5 - Penthouse Suite: Room 8 (501)

class BookingSeederLocation2 extends Seeder
{
    public function run(): void
    {
        $locationId = 2;

        $guests = $this->createGuests($locationId);
        $this->createBookings($locationId, $guests);
    }

    private function createGuests(int $locationId): array
    {
        return [
            Guest::create([
                'location_id' => $locationId,
                'full_name'   => 'Lucas Müller',
                'email'       => 'lucas.muller@email.com',
                'phone'       => '+49-151-23456789',
                'nationality' => 'German',
                'id_type'     => 'passport',
                'id_number'   => 'DE-LM5005',
                'address'     => 'Berlin, Germany',
            ]),
            Guest::create([
                'location_id' => $locationId,
                'full_name'   => 'Anita Gurung',
                'email'       => 'anita.gurung@email.com',
                'phone'       => '+977-9841234503',
                'nationality' => 'Nepali',
                'id_type'     => 'citizenship',
                'id_number'   => 'CIT-C3003',
                'address'     => 'Butwal, Nepal',
            ]),
            Guest::create([
                'location_id' => $locationId,
                'full_name'   => 'Hiroshi Tanaka',
                'email'       => 'hiroshi.tanaka@email.com',
                'phone'       => '+81-90-1234-5678',
                'nationality' => 'Japanese',
                'id_type'     => 'passport',
                'id_number'   => 'JP-HT6006',
                'address'     => 'Tokyo, Japan',
            ]),
            Guest::create([
                'location_id' => $locationId,
                'full_name'   => 'Fatima Al-Hassan',
                'email'       => 'fatima.alhassan@email.com',
                'phone'       => '+971-50-123-4567',
                'nationality' => 'Emirati',
                'id_type'     => 'passport',
                'id_number'   => 'AE-FA7007',
                'address'     => 'Dubai, UAE',
            ]),
        ];
    }

    private function createBookings(int $locationId, array $guests): void
    {
        $bookings = [
            // Room 6 (101) — Junior Suite — past stay
            [
                'booking_ref'    => 'DWELL-L2-0001',
                'guest_id'       => $guests[0]->id,
                'room_type_id'   => 4,
                'room_id'        => 6,
                'location_id'    => $locationId,
                'check_in_date'  => now()->subDays(45)->toDateString(),
                'check_out_date' => now()->subDays(40)->toDateString(),
                'num_guests'     => 1,
                'stay_type'      => 'short_term',
                'rate_per_night' => 7000.00,
                'total_amount'   => 35000.00,
                'deposit_amount' => 14000.00,
                'amount_paid'    => 35000.00,
                'status'         => 'checked_out',
                'payment_status' => 'fully_paid',
                'checked_in_at'  => now()->subDays(45),
                'checked_out_at' => now()->subDays(40),
            ],
            // Room 6 (101) — Junior Suite — currently checked in (long term)
            [
                'booking_ref'    => 'DWELL-L2-0002',
                'guest_id'       => $guests[1]->id,
                'room_type_id'   => 4,
                'room_id'        => 6,
                'location_id'    => $locationId,
                'check_in_date'  => now()->subDays(6)->toDateString(),
                'check_out_date' => now()->addDays(24)->toDateString(),
                'num_guests'     => 2,
                'stay_type'      => 'long_term',
                'rate_per_month' => 140000.00,
                'total_amount'   => 140000.00,
                'deposit_amount' => 28000.00,
                'amount_paid'    => 28000.00,
                'status'         => 'checked_in',
                'payment_status' => 'deposit_paid',
                'checked_in_at'  => now()->subDays(6),
            ],
            // Room 7 (301) — Junior Suite — past stay
            [
                'booking_ref'    => 'DWELL-L2-0003',
                'guest_id'       => $guests[2]->id,
                'room_type_id'   => 4,
                'room_id'        => 7,
                'location_id'    => $locationId,
                'check_in_date'  => now()->subDays(14)->toDateString(),
                'check_out_date' => now()->subDays(7)->toDateString(),
                'num_guests'     => 2,
                'stay_type'      => 'short_term',
                'rate_per_night' => 7000.00,
                'total_amount'   => 49000.00,
                'deposit_amount' => 14000.00,
                'amount_paid'    => 49000.00,
                'status'         => 'checked_out',
                'payment_status' => 'fully_paid',
                'checked_in_at'  => now()->subDays(14),
                'checked_out_at' => now()->subDays(7),
            ],
            // Room 7 (301) — Junior Suite — future confirmed
            [
                'booking_ref'    => 'DWELL-L2-0004',
                'guest_id'       => $guests[3]->id,
                'room_type_id'   => 4,
                'room_id'        => 7,
                'location_id'    => $locationId,
                'check_in_date'  => now()->addDays(3)->toDateString(),
                'check_out_date' => now()->addDays(8)->toDateString(),
                'num_guests'     => 2,
                'stay_type'      => 'short_term',
                'rate_per_night' => 7000.00,
                'total_amount'   => 35000.00,
                'deposit_amount' => 14000.00,
                'amount_paid'    => 14000.00,
                'status'         => 'confirmed',
                'payment_status' => 'deposit_paid',
            ],
            // Room 8 (501) — Penthouse Suite — past stay
            [
                'booking_ref'    => 'DWELL-L2-0005',
                'guest_id'       => $guests[0]->id,
                'room_type_id'   => 5,
                'room_id'        => 8,
                'location_id'    => $locationId,
                'check_in_date'  => now()->subDays(50)->toDateString(),
                'check_out_date' => now()->subDays(45)->toDateString(),
                'num_guests'     => 2,
                'stay_type'      => 'short_term',
                'rate_per_night' => 15000.00,
                'total_amount'   => 75000.00,
                'deposit_amount' => 30000.00,
                'amount_paid'    => 75000.00,
                'status'         => 'checked_out',
                'payment_status' => 'fully_paid',
                'checked_in_at'  => now()->subDays(50),
                'checked_out_at' => now()->subDays(45),
            ],
            // Room 8 (501) — Penthouse Suite — future pending
            [
                'booking_ref'    => 'DWELL-L2-0006',
                'guest_id'       => $guests[2]->id,
                'room_type_id'   => 5,
                'room_id'        => 8,
                'location_id'    => $locationId,
                'check_in_date'  => now()->addDays(20)->toDateString(),
                'check_out_date' => now()->addDays(25)->toDateString(),
                'num_guests'     => 2,
                'stay_type'      => 'short_term',
                'rate_per_night' => 15000.00,
                'total_amount'   => 75000.00,
                'deposit_amount' => 30000.00,
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

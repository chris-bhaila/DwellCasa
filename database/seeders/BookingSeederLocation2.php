<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class BookingSeederLocation2 extends Seeder
{
    public function run(): void
    {
        $locationId = 2;

        $guests = $this->createGuests($locationId);
        $roomTypes = $this->createRoomTypes($locationId);
        $this->createBookings($locationId, $guests, $roomTypes);
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

    private function createRoomTypes(int $locationId): array
    {
        return [
            RoomType::firstOrCreate(
                ['slug' => 'superior-room-thamel'],
                [
                    'location_id'     => $locationId,
                    'name'            => 'Superior Room',
                    'description'     => 'Contemporary superior room with city views and premium bedding.',
                    'max_occupancy'   => 2,
                    'price_per_night' => 5000.00,
                    'price_per_month' => 100000.00,
                    'size_sqft'       => '400',
                    'is_active'       => true,
                    'sort_order'      => 1,
                ]
            ),
            RoomType::firstOrCreate(
                ['slug' => 'family-suite-thamel'],
                [
                    'location_id'     => $locationId,
                    'name'            => 'Family Suite',
                    'description'     => 'Spacious suite with separate living area, perfect for families.',
                    'max_occupancy'   => 4,
                    'price_per_night' => 8500.00,
                    'price_per_month' => 170000.00,
                    'size_sqft'       => '750',
                    'is_active'       => true,
                    'sort_order'      => 2,
                ]
            ),
        ];
    }

    private function createBookings(int $locationId, array $guests, array $roomTypes): void
    {
        $superior = $roomTypes[0];
        $family   = $roomTypes[1];

        $bookings = [
            [
                'booking_ref'    => 'DWELL-L2-0001',
                'guest_id'       => $guests[0]->id,
                'room_type_id'   => $superior->id,
                'location_id'    => $locationId,
                'check_in_date'  => now()->subDays(14)->toDateString(),
                'check_out_date' => now()->subDays(7)->toDateString(),
                'num_guests'     => 1,
                'stay_type'      => 'short_term',
                'rate_per_night' => 5000.00,
                'total_amount'   => 35000.00,
                'deposit_amount' => 10000.00,
                'amount_paid'    => 35000.00,
                'status'         => 'checked_out',
                'payment_status' => 'fully_paid',
                'checked_in_at'  => now()->subDays(14),
                'checked_out_at' => now()->subDays(7),
            ],
            [
                'booking_ref'    => 'DWELL-L2-0002',
                'guest_id'       => $guests[1]->id,
                'room_type_id'   => $family->id,
                'location_id'    => $locationId,
                'check_in_date'  => now()->subDays(5)->toDateString(),
                'check_out_date' => now()->addDays(25)->toDateString(),
                'num_guests'     => 3,
                'stay_type'      => 'long_term',
                'rate_per_month' => 170000.00,
                'total_amount'   => 170000.00,
                'deposit_amount' => 34000.00,
                'amount_paid'    => 34000.00,
                'status'         => 'checked_in',
                'payment_status' => 'deposit_paid',
                'checked_in_at'  => now()->subDays(5),
            ],
            [
                'booking_ref'    => 'DWELL-L2-0003',
                'guest_id'       => $guests[2]->id,
                'room_type_id'   => $superior->id,
                'location_id'    => $locationId,
                'check_in_date'  => now()->addDays(3)->toDateString(),
                'check_out_date' => now()->addDays(8)->toDateString(),
                'num_guests'     => 2,
                'stay_type'      => 'short_term',
                'rate_per_night' => 5000.00,
                'total_amount'   => 25000.00,
                'deposit_amount' => 10000.00,
                'amount_paid'    => 10000.00,
                'status'         => 'confirmed',
                'payment_status' => 'deposit_paid',
            ],
            [
                'booking_ref'    => 'DWELL-L2-0004',
                'guest_id'       => $guests[3]->id,
                'room_type_id'   => $family->id,
                'location_id'    => $locationId,
                'check_in_date'  => now()->addDays(20)->toDateString(),
                'check_out_date' => now()->addDays(24)->toDateString(),
                'num_guests'     => 4,
                'stay_type'      => 'short_term',
                'rate_per_night' => 8500.00,
                'total_amount'   => 34000.00,
                'deposit_amount' => 8500.00,
                'amount_paid'    => 0.00,
                'status'         => 'pending',
                'payment_status' => 'unpaid',
            ],
            [
                'booking_ref'    => 'DWELL-L2-0005',
                'guest_id'       => $guests[0]->id,
                'room_type_id'   => $family->id,
                'location_id'    => $locationId,
                'check_in_date'  => now()->subDays(45)->toDateString(),
                'check_out_date' => now()->subDays(41)->toDateString(),
                'num_guests'     => 2,
                'stay_type'      => 'short_term',
                'rate_per_night' => 8500.00,
                'total_amount'   => 34000.00,
                'deposit_amount' => 8500.00,
                'amount_paid'    => 34000.00,
                'status'         => 'checked_out',
                'payment_status' => 'fully_paid',
                'checked_in_at'  => now()->subDays(45),
                'checked_out_at' => now()->subDays(41),
            ],
            [
                'booking_ref'    => 'DWELL-L2-0006',
                'guest_id'       => $guests[2]->id,
                'room_type_id'   => $superior->id,
                'location_id'    => $locationId,
                'check_in_date'  => now()->subDays(20)->toDateString(),
                'check_out_date' => now()->subDays(18)->toDateString(),
                'num_guests'     => 1,
                'stay_type'      => 'short_term',
                'rate_per_night' => 5000.00,
                'total_amount'   => 10000.00,
                'deposit_amount' => 5000.00,
                'amount_paid'    => 5000.00,
                'status'         => 'cancelled',
                'payment_status' => 'refunded',
                'refund_amount'  => 5000.00,
                'refunded_at'    => now()->subDays(19),
            ],
        ];

        foreach ($bookings as $data) {
            Booking::create($data);
        }
    }
}

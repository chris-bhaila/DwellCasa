<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class BookingSeederLocation1 extends Seeder
{
    public function run(): void
    {
        $locationId = 1;

        $guests = $this->createGuests($locationId);
        $roomTypes = $this->createRoomTypes($locationId);
        $this->createBookings($locationId, $guests, $roomTypes);
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

    private function createRoomTypes(int $locationId): array
    {
        return [
            RoomType::firstOrCreate(
                ['slug' => 'deluxe-room-patan'],
                [
                    'location_id'     => $locationId,
                    'name'            => 'Deluxe Room',
                    'description'     => 'Spacious deluxe room with traditional Newari décor and modern amenities.',
                    'max_occupancy'   => 2,
                    'price_per_night' => 4500.00,
                    'price_per_month' => 90000.00,
                    'size_sqft'       => '350',
                    'is_active'       => true,
                    'sort_order'      => 1,
                ]
            ),
            RoomType::firstOrCreate(
                ['slug' => 'studio-apartment-patan'],
                [
                    'location_id'     => $locationId,
                    'name'            => 'Studio Apartment',
                    'description'     => 'Self-contained studio with kitchenette, ideal for extended stays.',
                    'max_occupancy'   => 2,
                    'price_per_night' => 6000.00,
                    'price_per_month' => 120000.00,
                    'size_sqft'       => '500',
                    'is_active'       => true,
                    'sort_order'      => 2,
                ]
            ),
        ];
    }

    private function createBookings(int $locationId, array $guests, array $roomTypes): void
    {
        $deluxe  = $roomTypes[0];
        $studio  = $roomTypes[1];

        $bookings = [
            [
                'booking_ref'    => 'DWELL-L1-0001',
                'guest_id'       => $guests[0]->id,
                'room_type_id'   => $deluxe->id,
                'location_id'    => $locationId,
                'check_in_date'  => now()->subDays(30)->toDateString(),
                'check_out_date' => now()->subDays(23)->toDateString(),
                'num_guests'     => 2,
                'stay_type'      => 'short_term',
                'rate_per_night' => 4500.00,
                'total_amount'   => 31500.00,
                'deposit_amount' => 9000.00,
                'amount_paid'    => 31500.00,
                'status'         => 'checked_out',
                'payment_status' => 'fully_paid',
                'checked_in_at'  => now()->subDays(30),
                'checked_out_at' => now()->subDays(23),
            ],
            [
                'booking_ref'    => 'DWELL-L1-0002',
                'guest_id'       => $guests[1]->id,
                'room_type_id'   => $studio->id,
                'location_id'    => $locationId,
                'check_in_date'  => now()->subDays(10)->toDateString(),
                'check_out_date' => now()->addDays(20)->toDateString(),
                'num_guests'     => 1,
                'stay_type'      => 'long_term',
                'rate_per_month' => 120000.00,
                'total_amount'   => 120000.00,
                'deposit_amount' => 24000.00,
                'amount_paid'    => 24000.00,
                'status'         => 'checked_in',
                'payment_status' => 'deposit_paid',
                'checked_in_at'  => now()->subDays(10),
            ],
            [
                'booking_ref'    => 'DWELL-L1-0003',
                'guest_id'       => $guests[2]->id,
                'room_type_id'   => $deluxe->id,
                'location_id'    => $locationId,
                'check_in_date'  => now()->addDays(5)->toDateString(),
                'check_out_date' => now()->addDays(10)->toDateString(),
                'num_guests'     => 2,
                'stay_type'      => 'short_term',
                'rate_per_night' => 4500.00,
                'total_amount'   => 22500.00,
                'deposit_amount' => 9000.00,
                'amount_paid'    => 9000.00,
                'status'         => 'confirmed',
                'payment_status' => 'deposit_paid',
            ],
            [
                'booking_ref'    => 'DWELL-L1-0004',
                'guest_id'       => $guests[3]->id,
                'room_type_id'   => $studio->id,
                'location_id'    => $locationId,
                'check_in_date'  => now()->addDays(15)->toDateString(),
                'check_out_date' => now()->addDays(18)->toDateString(),
                'num_guests'     => 1,
                'stay_type'      => 'short_term',
                'rate_per_night' => 6000.00,
                'total_amount'   => 18000.00,
                'deposit_amount' => 6000.00,
                'amount_paid'    => 0.00,
                'status'         => 'pending',
                'payment_status' => 'unpaid',
            ],
            [
                'booking_ref'    => 'DWELL-L1-0005',
                'guest_id'       => $guests[0]->id,
                'room_type_id'   => $studio->id,
                'location_id'    => $locationId,
                'check_in_date'  => now()->subDays(60)->toDateString(),
                'check_out_date' => now()->subDays(55)->toDateString(),
                'num_guests'     => 2,
                'stay_type'      => 'short_term',
                'rate_per_night' => 6000.00,
                'total_amount'   => 30000.00,
                'deposit_amount' => 6000.00,
                'amount_paid'    => 30000.00,
                'status'         => 'checked_out',
                'payment_status' => 'fully_paid',
                'checked_in_at'  => now()->subDays(60),
                'checked_out_at' => now()->subDays(55),
            ],
            [
                'booking_ref'    => 'DWELL-L1-0006',
                'guest_id'       => $guests[1]->id,
                'room_type_id'   => $deluxe->id,
                'location_id'    => $locationId,
                'check_in_date'  => now()->subDays(5)->toDateString(),
                'check_out_date' => now()->addDays(2)->toDateString(),
                'num_guests'     => 1,
                'stay_type'      => 'short_term',
                'rate_per_night' => 4500.00,
                'discount'       => 1500.00,
                'total_amount'   => 27000.00,
                'deposit_amount' => 9000.00,
                'amount_paid'    => 18000.00,
                'status'         => 'checked_in',
                'payment_status' => 'partially_paid',
                'checked_in_at'  => now()->subDays(5),
            ],
        ];

        foreach ($bookings as $data) {
            Booking::create($data);
        }
    }
}

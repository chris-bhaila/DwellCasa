<?php

namespace Database\Seeders;

use App\Models\InventoryCategory;
use App\Models\InventoryEquipment;
use App\Models\InventoryItem;
use App\Models\InventoryStock;
use App\Models\User;
use Illuminate\Database\Seeder;

class InventorySeederLocation3 extends Seeder
{
    public function run(): void
    {
        $locationId  = 3;
        $performedBy = User::first()?->id ?? 1;

        // ── Supply categories ──────────────────────────────────────
        $gymSupplies = InventoryCategory::create([
            'location_id' => $locationId,
            'name'        => 'Gym Supplies',
            'type'        => 'supply',
        ]);

        $firstAid = InventoryCategory::create([
            'location_id' => $locationId,
            'name'        => 'First Aid & Safety',
            'type'        => 'supply',
        ]);

        $cleaning = InventoryCategory::create([
            'location_id' => $locationId,
            'name'        => 'Cleaning & Hygiene',
            'type'        => 'supply',
        ]);

        // ── Equipment categories ───────────────────────────────────
        $cardio = InventoryCategory::create([
            'location_id' => $locationId,
            'name'        => 'Cardio Equipment',
            'type'        => 'equipment',
        ]);

        $strength = InventoryCategory::create([
            'location_id' => $locationId,
            'name'        => 'Strength Equipment',
            'type'        => 'equipment',
        ]);

        // ── Supply items + stock ───────────────────────────────────
        $supplies = [
            [
                'category_id'   => $gymSupplies->id,
                'name'          => 'Resistance Bands (Set)',
                'unit'          => 'sets',
                'minimum_stock' => 10,
                'quantity'      => 20,
                'cost'          => 4000,
                'status'        => 'available',
            ],
            [
                'category_id'   => $gymSupplies->id,
                'name'          => 'Yoga Mats',
                'unit'          => 'pieces',
                'minimum_stock' => 15,
                'quantity'      => 12,
                'cost'          => 3600,
                'status'        => 'low_stock',
            ],
            [
                'category_id'   => $gymSupplies->id,
                'name'          => 'Jump Ropes',
                'unit'          => 'pieces',
                'minimum_stock' => 10,
                'quantity'      => 0,
                'cost'          => 0,
                'status'        => 'out_of_stock',
            ],
            [
                'category_id'   => $firstAid->id,
                'name'          => 'First Aid Kit (Refill Pack)',
                'unit'          => 'packs',
                'minimum_stock' => 3,
                'quantity'      => 5,
                'cost'          => 1500,
                'status'        => 'available',
            ],
            [
                'category_id'   => $firstAid->id,
                'name'          => 'Ice Packs (Instant)',
                'unit'          => 'pieces',
                'minimum_stock' => 10,
                'quantity'      => 8,
                'cost'          => 640,
                'status'        => 'low_stock',
            ],
            [
                'category_id'   => $cleaning->id,
                'name'          => 'Equipment Disinfectant Spray (1L)',
                'unit'          => 'bottles',
                'minimum_stock' => 10,
                'quantity'      => 22,
                'cost'          => 2200,
                'status'        => 'available',
            ],
            [
                'category_id'   => $cleaning->id,
                'name'          => 'Microfiber Cleaning Cloths',
                'unit'          => 'pieces',
                'minimum_stock' => 20,
                'quantity'      => 30,
                'cost'          => 900,
                'status'        => 'available',
            ],
        ];

        foreach ($supplies as $s) {
            $item = InventoryItem::create([
                'location_id'   => $locationId,
                'category_id'   => $s['category_id'],
                'name'          => $s['name'],
                'type'          => 'supply',
                'unit'          => $s['unit'],
                'minimum_stock' => $s['minimum_stock'],
            ]);

            InventoryStock::create([
                'location_id'       => $locationId,
                'inventory_item_id' => $item->id,
                'quantity_on_hand'  => $s['quantity'],
                'total_cost'        => $s['cost'],
                'status'            => $s['status'],
            ]);
        }

        // ── Equipment items + units ────────────────────────────────
        $treadmill = InventoryItem::create([
            'location_id' => $locationId,
            'category_id' => $cardio->id,
            'name'        => 'Treadmill (Commercial Grade)',
            'type'        => 'equipment',
            'description' => 'Heavy-duty motorized treadmill for cardio floor',
        ]);

        foreach ([
            ['condition' => 'new',          'status' => 'assigned',    'serial' => 'TM-001', 'cost' => 75000],
            ['condition' => 'good',         'status' => 'assigned',    'serial' => 'TM-002', 'cost' => 75000],
            ['condition' => 'good',         'status' => 'assigned',    'serial' => 'TM-003', 'cost' => 75000],
            ['condition' => 'under_repair', 'status' => 'maintenance', 'serial' => 'TM-004', 'cost' => 75000],
        ] as $u) {
            InventoryEquipment::create([
                'location_id'       => $locationId,
                'inventory_item_id' => $treadmill->id,
                'serial_number'     => $u['serial'],
                'condition'         => $u['condition'],
                'status'            => $u['status'],
                'purchase_cost'     => $u['cost'],
                'purchased_at'      => now()->subMonths(rand(3, 24)),
            ]);
        }

        $spinBike = InventoryItem::create([
            'location_id' => $locationId,
            'category_id' => $cardio->id,
            'name'        => 'Spin Bike (Studio)',
            'type'        => 'equipment',
            'description' => 'Studio-grade spin bikes for cycling classes',
        ]);

        foreach ([
            ['condition' => 'good', 'status' => 'assigned',   'serial' => 'SB-001', 'cost' => 30000],
            ['condition' => 'good', 'status' => 'assigned',   'serial' => 'SB-002', 'cost' => 30000],
            ['condition' => 'good', 'status' => 'assigned',   'serial' => 'SB-003', 'cost' => 30000],
            ['condition' => 'fair', 'status' => 'available',  'serial' => 'SB-004', 'cost' => 30000],
        ] as $u) {
            InventoryEquipment::create([
                'location_id'       => $locationId,
                'inventory_item_id' => $spinBike->id,
                'serial_number'     => $u['serial'],
                'condition'         => $u['condition'],
                'status'            => $u['status'],
                'purchase_cost'     => $u['cost'],
                'purchased_at'      => now()->subMonths(rand(6, 20)),
            ]);
        }

        $cableMachine = InventoryItem::create([
            'location_id' => $locationId,
            'category_id' => $strength->id,
            'name'        => 'Dual Cable Crossover Machine',
            'type'        => 'equipment',
            'description' => 'Dual-stack cable machine for functional strength training',
        ]);

        foreach ([
            ['condition' => 'good',         'status' => 'assigned',    'serial' => 'CC-001', 'cost' => 120000],
            ['condition' => 'under_repair', 'status' => 'maintenance', 'serial' => 'CC-002', 'cost' => 120000],
        ] as $u) {
            InventoryEquipment::create([
                'location_id'       => $locationId,
                'inventory_item_id' => $cableMachine->id,
                'serial_number'     => $u['serial'],
                'condition'         => $u['condition'],
                'status'            => $u['status'],
                'purchase_cost'     => $u['cost'],
                'purchased_at'      => now()->subMonths(rand(12, 36)),
            ]);
        }

        $dumbbellRack = InventoryItem::create([
            'location_id' => $locationId,
            'category_id' => $strength->id,
            'name'        => 'Dumbbell Rack with Weights (5–30kg)',
            'type'        => 'equipment',
            'description' => 'Full dumbbell rack set for free weights area',
        ]);

        foreach ([
            ['condition' => 'good', 'status' => 'assigned',  'serial' => null, 'cost' => 45000],
            ['condition' => 'good', 'status' => 'assigned',  'serial' => null, 'cost' => 45000],
            ['condition' => 'fair', 'status' => 'available', 'serial' => null, 'cost' => 45000],
        ] as $u) {
            InventoryEquipment::create([
                'location_id'       => $locationId,
                'inventory_item_id' => $dumbbellRack->id,
                'serial_number'     => $u['serial'],
                'condition'         => $u['condition'],
                'status'            => $u['status'],
                'purchase_cost'     => $u['cost'],
                'purchased_at'      => now()->subMonths(rand(6, 30)),
            ]);
        }
    }
}
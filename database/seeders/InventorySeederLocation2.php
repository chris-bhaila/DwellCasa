<?php

namespace Database\Seeders;

use App\Models\InventoryCategory;
use App\Models\InventoryEquipment;
use App\Models\InventoryItem;
use App\Models\InventoryStock;
use App\Models\User;
use Illuminate\Database\Seeder;

class InventorySeederLocation2 extends Seeder
{
    public function run(): void
    {
        $locationId  = 2;
        $performedBy = User::first()?->id ?? 1;

        // ── Supply categories ──────────────────────────────────────
        $kitchen = InventoryCategory::create([
            'location_id' => $locationId,
            'name'        => 'Kitchen Supplies',
            'type'        => 'supply',
        ]);

        $dining = InventoryCategory::create([
            'location_id' => $locationId,
            'name'        => 'Dining & Tableware',
            'type'        => 'supply',
        ]);

        $cleaning = InventoryCategory::create([
            'location_id' => $locationId,
            'name'        => 'Cleaning & Sanitation',
            'type'        => 'supply',
        ]);

        // ── Equipment categories ───────────────────────────────────
        $kitchenEquip = InventoryCategory::create([
            'location_id' => $locationId,
            'name'        => 'Kitchen Equipment',
            'type'        => 'equipment',
        ]);

        $diningFurniture = InventoryCategory::create([
            'location_id' => $locationId,
            'name'        => 'Dining Furniture',
            'type'        => 'equipment',
        ]);

        // ── Supply items + stock ───────────────────────────────────
        $supplies = [
            [
                'category_id'   => $kitchen->id,
                'name'          => 'Disposable Gloves (Box)',
                'unit'          => 'boxes',
                'minimum_stock' => 10,
                'quantity'      => 25,
                'cost'          => 3750,
                'status'        => 'available',
            ],
            [
                'category_id'   => $kitchen->id,
                'name'          => 'Aluminum Foil Roll',
                'unit'          => 'rolls',
                'minimum_stock' => 5,
                'quantity'      => 4,
                'cost'          => 600,
                'status'        => 'low_stock',
            ],
            [
                'category_id'   => $kitchen->id,
                'name'          => 'Food Storage Containers',
                'unit'          => 'pieces',
                'minimum_stock' => 20,
                'quantity'      => 35,
                'cost'          => 1750,
                'status'        => 'available',
            ],
            [
                'category_id'   => $dining->id,
                'name'          => 'Dinner Plates (10")',
                'unit'          => 'pieces',
                'minimum_stock' => 30,
                'quantity'      => 60,
                'cost'          => 6000,
                'status'        => 'available',
            ],
            [
                'category_id'   => $dining->id,
                'name'          => 'Stainless Cutlery Set',
                'unit'          => 'sets',
                'minimum_stock' => 20,
                'quantity'      => 15,
                'cost'          => 3000,
                'status'        => 'low_stock',
            ],
            [
                'category_id'   => $dining->id,
                'name'          => 'Water Glasses',
                'unit'          => 'pieces',
                'minimum_stock' => 30,
                'quantity'      => 0,
                'cost'          => 0,
                'status'        => 'out_of_stock',
            ],
            [
                'category_id'   => $cleaning->id,
                'name'          => 'Dishwashing Liquid (1L)',
                'unit'          => 'bottles',
                'minimum_stock' => 10,
                'quantity'      => 18,
                'cost'          => 1440,
                'status'        => 'available',
            ],
            [
                'category_id'   => $cleaning->id,
                'name'          => 'Sanitizing Spray (500ml)',
                'unit'          => 'bottles',
                'minimum_stock' => 12,
                'quantity'      => 9,
                'cost'          => 1080,
                'status'        => 'low_stock',
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
        $gasRange = InventoryItem::create([
            'location_id' => $locationId,
            'category_id' => $kitchenEquip->id,
            'name'        => 'Commercial Gas Range (6-Burner)',
            'type'        => 'equipment',
            'description' => 'Heavy-duty gas range for main kitchen line',
        ]);

        foreach ([
            ['condition' => 'good', 'status' => 'assigned',    'serial' => 'GR-001', 'cost' => 85000],
            ['condition' => 'fair', 'status' => 'assigned',    'serial' => 'GR-002', 'cost' => 85000],
            ['condition' => 'good', 'status' => 'maintenance', 'serial' => 'GR-003', 'cost' => 85000],
        ] as $u) {
            InventoryEquipment::create([
                'location_id'       => $locationId,
                'inventory_item_id' => $gasRange->id,
                'serial_number'     => $u['serial'],
                'condition'         => $u['condition'],
                'status'            => $u['status'],
                'purchase_cost'     => $u['cost'],
                'purchased_at'      => now()->subMonths(rand(6, 30)),
            ]);
        }

        $refrigerator = InventoryItem::create([
            'location_id' => $locationId,
            'category_id' => $kitchenEquip->id,
            'name'        => 'Upright Refrigerator (Commercial)',
            'type'        => 'equipment',
            'description' => 'Stainless steel upright fridge for ingredient storage',
        ]);

        foreach ([
            ['condition' => 'new',  'status' => 'assigned',   'serial' => 'RF-001', 'cost' => 55000],
            ['condition' => 'good', 'status' => 'assigned',   'serial' => 'RF-002', 'cost' => 55000],
            ['condition' => 'fair', 'status' => 'available',  'serial' => 'RF-003', 'cost' => 55000],
        ] as $u) {
            InventoryEquipment::create([
                'location_id'       => $locationId,
                'inventory_item_id' => $refrigerator->id,
                'serial_number'     => $u['serial'],
                'condition'         => $u['condition'],
                'status'            => $u['status'],
                'purchase_cost'     => $u['cost'],
                'purchased_at'      => now()->subMonths(rand(3, 18)),
            ]);
        }

        $diningTable = InventoryItem::create([
            'location_id' => $locationId,
            'category_id' => $diningFurniture->id,
            'name'        => 'Dining Table (4-Seater)',
            'type'        => 'equipment',
            'description' => 'Wooden dining tables for the main floor',
        ]);

        foreach ([
            ['condition' => 'good', 'status' => 'assigned',  'serial' => null, 'cost' => 8000],
            ['condition' => 'good', 'status' => 'assigned',  'serial' => null, 'cost' => 8000],
            ['condition' => 'good', 'status' => 'assigned',  'serial' => null, 'cost' => 8000],
            ['condition' => 'fair', 'status' => 'assigned',  'serial' => null, 'cost' => 8000],
            ['condition' => 'fair', 'status' => 'available', 'serial' => null, 'cost' => 8000],
        ] as $u) {
            InventoryEquipment::create([
                'location_id'       => $locationId,
                'inventory_item_id' => $diningTable->id,
                'serial_number'     => $u['serial'],
                'condition'         => $u['condition'],
                'status'            => $u['status'],
                'purchase_cost'     => $u['cost'],
                'purchased_at'      => now()->subMonths(rand(12, 48)),
            ]);
        }
    }
}
<?php

namespace Database\Seeders;

use App\Models\InventoryCategory;
use App\Models\InventoryEquipment;
use App\Models\InventoryItem;
use App\Models\InventoryStock;
use App\Models\User;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $locationId  = 1;
        $performedBy = User::first()?->id ?? 1;

        // ── Supply categories ──────────────────────────────────────
        $housekeeping = InventoryCategory::create([
            'location_id' => $locationId,
            'name'        => 'Housekeeping',
            'type'        => 'supply',
        ]);

        $toiletries = InventoryCategory::create([
            'location_id' => $locationId,
            'name'        => 'Toiletries',
            'type'        => 'supply',
        ]);

        $maintenance = InventoryCategory::create([
            'location_id' => $locationId,
            'name'        => 'Maintenance',
            'type'        => 'supply',
        ]);

        // ── Equipment categories ───────────────────────────────────
        $furniture = InventoryCategory::create([
            'location_id' => $locationId,
            'name'        => 'Furniture',
            'type'        => 'equipment',
        ]);

        $electronics = InventoryCategory::create([
            'location_id' => $locationId,
            'name'        => 'Electronics',
            'type'        => 'equipment',
        ]);

        // ── Supply items + stock ───────────────────────────────────
        $supplies = [
            [
                'category_id'   => $housekeeping->id,
                'name'          => 'Bed Sheets (Double)',
                'unit'          => 'pieces',
                'minimum_stock' => 10,
                'quantity'      => 24,
                'cost'          => 2400,
                'status'        => 'available',
            ],
            [
                'category_id'   => $housekeeping->id,
                'name'          => 'Pillow Cases',
                'unit'          => 'pieces',
                'minimum_stock' => 20,
                'quantity'      => 18,
                'cost'          => 900,
                'status'        => 'low_stock',
            ],
            [
                'category_id'   => $housekeeping->id,
                'name'          => 'Bath Towels',
                'unit'          => 'pieces',
                'minimum_stock' => 15,
                'quantity'      => 0,
                'cost'          => 0,
                'status'        => 'out_of_stock',
            ],
            [
                'category_id'   => $toiletries->id,
                'name'          => 'Hand Soap',
                'unit'          => 'bottles',
                'minimum_stock' => 20,
                'quantity'      => 45,
                'cost'          => 1350,
                'status'        => 'available',
            ],
            [
                'category_id'   => $toiletries->id,
                'name'          => 'Shampoo Sachets',
                'unit'          => 'pieces',
                'minimum_stock' => 50,
                'quantity'      => 38,
                'cost'          => 760,
                'status'        => 'low_stock',
            ],
            [
                'category_id'   => $maintenance->id,
                'name'          => 'Light Bulbs (LED)',
                'unit'          => 'pieces',
                'minimum_stock' => 10,
                'quantity'      => 22,
                'cost'          => 1100,
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
                'location_id'      => $locationId,
                'inventory_item_id'=> $item->id,
                'quantity_on_hand' => $s['quantity'],
                'total_cost'       => $s['cost'],
                'status'           => $s['status'],
            ]);
        }

        // ── Equipment items + units ────────────────────────────────
        $television = InventoryItem::create([
            'location_id' => $locationId,
            'category_id' => $electronics->id,
            'name'        => 'Television (32")',
            'type'        => 'equipment',
            'description' => '32-inch flat-screen TV for guest rooms',
        ]);

        $units = [
            ['condition' => 'good',         'status' => 'assigned',   'serial' => 'TV-001', 'cost' => 15000],
            ['condition' => 'good',         'status' => 'assigned',   'serial' => 'TV-002', 'cost' => 15000],
            ['condition' => 'fair',         'status' => 'available',  'serial' => 'TV-003', 'cost' => 15000],
            ['condition' => 'under_repair', 'status' => 'maintenance','serial' => 'TV-004', 'cost' => 15000],
        ];

        foreach ($units as $u) {
            InventoryEquipment::create([
                'location_id'      => $locationId,
                'inventory_item_id'=> $television->id,
                'serial_number'    => $u['serial'],
                'condition'        => $u['condition'],
                'status'           => $u['status'],
                'purchase_cost'    => $u['cost'],
                'purchased_at'     => now()->subMonths(rand(3, 18)),
            ]);
        }

        $aircon = InventoryItem::create([
            'location_id' => $locationId,
            'category_id' => $electronics->id,
            'name'        => 'Air Conditioner (1.5 Ton)',
            'type'        => 'equipment',
            'description' => 'Split AC units installed in rooms',
        ]);

        $acUnits = [
            ['condition' => 'new',     'status' => 'assigned',  'serial' => 'AC-001', 'cost' => 45000],
            ['condition' => 'good',    'status' => 'assigned',  'serial' => 'AC-002', 'cost' => 45000],
            ['condition' => 'damaged', 'status' => 'available', 'serial' => 'AC-003', 'cost' => 45000],
        ];

        foreach ($acUnits as $u) {
            InventoryEquipment::create([
                'location_id'      => $locationId,
                'inventory_item_id'=> $aircon->id,
                'serial_number'    => $u['serial'],
                'condition'        => $u['condition'],
                'status'           => $u['status'],
                'purchase_cost'    => $u['cost'],
                'purchased_at'     => now()->subMonths(rand(6, 24)),
            ]);
        }

        $wardrobe = InventoryItem::create([
            'location_id' => $locationId,
            'category_id' => $furniture->id,
            'name'        => 'Wardrobe (3-Door)',
            'type'        => 'equipment',
        ]);

        $wardrobeUnits = [
            ['condition' => 'good', 'status' => 'assigned',  'serial' => null, 'cost' => 12000],
            ['condition' => 'good', 'status' => 'assigned',  'serial' => null, 'cost' => 12000],
            ['condition' => 'fair', 'status' => 'available', 'serial' => null, 'cost' => 12000],
        ];

        foreach ($wardrobeUnits as $u) {
            InventoryEquipment::create([
                'location_id'      => $locationId,
                'inventory_item_id'=> $wardrobe->id,
                'serial_number'    => $u['serial'],
                'condition'        => $u['condition'],
                'status'           => $u['status'],
                'purchase_cost'    => $u['cost'],
                'purchased_at'     => now()->subMonths(rand(12, 36)),
            ]);
        }
    }
}

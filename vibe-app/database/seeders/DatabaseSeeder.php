<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Customer::firstOrCreate(['name' => 'Walk-in Customer'], ['type' => 'default', 'status' => 'active']);
        foreach ([
            ['Rice 25kg (Sinandomeng)', 'RICE-25', 1250, 24, 4],
            ['Cooking Oil 1L', 'OIL-1L', 185, 24, 5],
            ['Instant Coffee Twin Pack', 'COFFEE-2', 120, 150, 20],
            ['Canned Sardines 155g', 'SARD-155', 45, 36, 8],
        ] as [$name, $sku, $price, $stock, $reorderLevel]) {
            Product::firstOrCreate(['sku' => $sku], ['name' => $name, 'sku' => $sku, 'price' => $price, 'stock' => $stock, 'reorder_level' => $reorderLevel, 'status' => 'active']);
        }
    }
}

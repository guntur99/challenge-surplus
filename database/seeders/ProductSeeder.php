<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'name'              => 'Product 2',
                'description'       => 'Product 2',
                'enable'            => 1,
                'created_at'        => now(),
                'updated_at'        => now()
            ],
        ]);

        $lastProduct = DB::table('products')->latest()->first();

        DB::table('category_product')->insert([
            [
                'category_id' => 1,
                'product_id'  => $lastProduct->id,
                'created_at'  => now(),
                'updated_at'  => now()
            ],
        ]);

        DB::table('images')->insert([
            [
                'name'        => 'Product Image 1',
                'enable'      => 1,
                'file'        => 'assets/dummy.jpeg',
                'created_at'  => now(),
                'updated_at'  => now()
            ],
        ]);

        $lastImage = DB::table('images')->latest()->first();

        DB::table('product_image')->insert([
            [
                'image_id'   => $lastImage->id,
                'product_id' => $lastProduct->id,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}

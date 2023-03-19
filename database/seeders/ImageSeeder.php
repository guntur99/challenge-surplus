<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageSeeder extends Seeder
{
    public function run(): void
    {
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
                'product_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}

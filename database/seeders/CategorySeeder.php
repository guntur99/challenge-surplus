<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name'   => 'Category 2',
                'enable' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}

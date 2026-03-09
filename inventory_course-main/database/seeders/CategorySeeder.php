<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Motherboards',
            'Processors',
            'Graphics Cards',
            'RAM Modules',
            'Power Supplies',
            'Monitors',
            'Keyboards',
            'Mice',
            'Storage Drives',
            'Cooling Systems',
        ];
        foreach ($categories as $name) {
            Category::create(['name' => $name]);
        }
    }
}

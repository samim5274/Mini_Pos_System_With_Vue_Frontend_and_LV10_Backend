<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Excategory;

class ExcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Office',
            'Transport',
            'Food',
            'Utilities',
            'Maintenance',
            'Marketing',
        ];

        foreach ($categories as $name) {
            Excategory::create([
                'name' => $name
            ]);
        }
    }
}

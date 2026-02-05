<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['Double A A4 Paper', 450],
            ['Ball Pen Blue', 12],
            ['Ball Pen Black', 12],
            ['Gel Pen', 20],
            ['Notebook Small', 60],
            ['Notebook Large', 120],
            ['Stapler Machine', 250],
            ['Staple Pin Box', 35],
            ['Eraser Soft', 8],
            ['Sharpener Metal', 15],
            ['Pencil HB', 10],
            ['Marker Permanent', 45],
            ['White Board Marker', 60],
            ['Calculator Basic', 450],
            ['Calculator Scientific', 950],
            ['USB Flash 16GB', 450],
            ['USB Flash 32GB', 650],
            ['Wireless Mouse', 550],
            ['Keyboard Standard', 800],
            ['Keyboard Mechanical', 1800],
            ['Headphone Wired', 400],
            ['Headphone Bluetooth', 1200],
            ['Office Chair', 6500],
            ['Desk Table', 12000],
            ['File Folder', 25],
            ['Paper Clip Box', 30],
            ['Sticky Notes', 40],
            ['Printer Ink Black', 1200],
            ['Printer Ink Color', 1500],
            ['Laptop Stand', 900],
        ];

        foreach ($products as $index => $item) {

            Product::create([
                'name' => $item[0],
                'sku' => 'SKU-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'price' => $item[1],
                'stock_quantity' => rand(20, 50),
                'min_stock' => rand(5, 15),
                'image' => null,
            ]);
        }
    }
}

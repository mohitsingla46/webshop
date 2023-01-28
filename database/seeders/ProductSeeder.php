<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::truncate();
        $arr = [];
        $csvFile = fopen(base_path("database/data/products.csv"), "r");

        $firstline = true;
        while (($data = fgetcsv($csvFile)) !== false) {
            if (!$firstline) {
                $arr[] = [
                    'id' => $data[0],
                    'productname' => $data[1],
                    'price' => $data[2],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $firstline = false;
        }

        fclose($csvFile);
        foreach (array_chunk($arr,1000) as $t)  
        {
            DB::table('products')->insert($t);
        }
        Log::info(count($arr)." records inserted in products table");
    }
}

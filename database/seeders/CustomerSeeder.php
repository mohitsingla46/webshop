<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::truncate();
        $arr = [];
        $csvFile = fopen(base_path("database/data/customers.csv"), "r");

        $firstline = true;
        while (($data = fgetcsv($csvFile)) !== false) {
            if (!$firstline) {
                $arr[] = [
                    'id' => $data[0],
                    'job_title' => $data[1],
                    'email' => $data[2],
                    'name' => $data[3],
                    'registered_since' => $data[4],
                    'phone' => $data[5],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $firstline = false;
        }

        fclose($csvFile);
        foreach (array_chunk($arr,1000) as $t)  
        {
            DB::table('customers')->insert($t);
        }
        Log::info(count($arr)." records inserted in customers table");
    }
}

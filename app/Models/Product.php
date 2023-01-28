<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'productname', 'price'
    ];

    public static function createOrUpdate($data, $product = null)
    {
        try{
            if (is_null($product)) {
                $product = new Product;
            }
            if (isset($data['productname'])) {
                $product->productname = $data['productname'];
            }
            if (isset($data['price'])) {
                $product->price = $data['price'];
            }
            $product->save();
            return ['status' => true, 'data' => $product];
        }
        catch(\Exception $e){
            return ['status' => false, 'message' => 'Something went Wrong!'];
        }
    }
}

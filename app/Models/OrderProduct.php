<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'product_id'
    ];

    public static function createOrUpdate($data, $order_product = null)
    {
        try{
            if (is_null($order_product)) {
                $order_product = new OrderProduct;
            }
            if (isset($data['order_id'])) {
                $order_product->order_id = $data['order_id'];
            }
            if (isset($data['product_id'])) {
                $order_product->product_id = $data['product_id'];
            }
            $order_product->save();
            return ['status' => true, 'data' => $order_product];
        }
        catch(\Exception $e){
            return ['status' => false, 'message' => 'Something went Wrong!'];
        }
    }

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }
}

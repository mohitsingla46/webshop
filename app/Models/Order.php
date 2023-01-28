<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'paid', 'total'
    ];

    public static function createOrUpdate($data, $order = null)
    {
        try{
            if (is_null($order)) {
                $order = new Order;
            }
            if (isset($data['customer_id'])) {
                $order->customer_id = $data['customer_id'];
            }
            if (isset($data['paid'])) {
                $order->paid = $data['paid'];
            }
            if (isset($data['total'])) {
                $order->total = $data['total'];
            }
            $order->save();
            return ['status' => true, 'data' => $order];
        }
        catch(\Exception $e){
            return ['status' => false, 'message' => 'Something went Wrong!'];
        }
    }

    public function order_products(){
        return $this->hasMany(OrderProduct::class, 'order_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_title', 'email', 'name', 'registered_since', 'phone'
    ];

    public static function createOrUpdate($data, $customer = null)
    {
        try{
            if (is_null($customer)) {
                $customer = new Customer;
            }
            if (isset($data['job_title'])) {
                $customer->job_title = $data['job_title'];
            }
            if (isset($data['email'])) {
                $customer->email = $data['email'];
            }
            if (isset($data['name'])) {
                $customer->name = $data['name'];
            }
            if (isset($data['registered_since'])) {
                $customer->registered_since = $data['registered_since'];
            }
            if (isset($data['phone'])) {
                $customer->phone = $data['phone'];
            }
            $customer->save();
            return ['status' => true, 'data' => $customer];
        }
        catch(\Exception $e){
            return ['status' => false, 'message' => 'Something went Wrong!'];
        }
    }
}

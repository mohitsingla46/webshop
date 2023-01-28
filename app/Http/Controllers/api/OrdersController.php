<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orders = Order::with('order_products.product')->where('customer_id', $request->customer_id)->get();
        return response()->json(['orders' => $orders]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }
        else{
            $price = Product::where('id', $request->product_id)->value('price');
            $data['customer_id'] = $request->customer_id;
            $data['paid'] = 0;
            $data['total'] = $price;
            $order = Order::createOrUpdate($data);
            if($order['status']){
                $pdata = [
                    'order_id' => $order['data']['id'],
                    'product_id' => $request->product_id
                ];
                OrderProduct::createOrUpdate($pdata);
            }
            $new_order = Order::with('order_products.product')->where('id', $order['data']['id'])->first();
            return response()->json(['order' => $new_order]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Order::where('id', $id)->delete();
        OrderProduct::where('order_id', $id)->delete();
        return response()->json(['message' => "Order has been deleted successfully!"]);
    }

    public function add_product_to_order($order_id, Request $request){
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }
        else{
            $orderExist = Order::where('id', $order_id)->first();
            if($orderExist){
                if($orderExist->paid == 1){
                    return response()->json(['error' => "Order is already paid!"], 400);
                }
                else{
                    $price = Product::where('id', $request->product_id)->value('price');
                    $data['customer_id'] = $request->customer_id;
                    $data['paid'] = 0;
                    $data['total'] = $orderExist->total + $price;
                    $order = Order::createOrUpdate($data, $orderExist);
                    if($order['status']){
                        $pdata = [
                            'order_id' => $order['data']['id'],
                            'product_id' => $request->product_id
                        ];
                        OrderProduct::createOrUpdate($pdata);
                    }
                    $new_order = Order::with('order_products.product')->where('id', $order['data']['id'])->first();
                    return response()->json(['order' => $new_order]);
                }
            }
            else{
                return response()->json(['error' => "Order does not exist!"], 400);
            }
        }
    }

    public function pay_order($order_id){
        $order = Order::where('id', $order_id)->first();
        if($order){
            $customer = Customer::where('id', $order->customer_id)->first();
            if($customer){
                $data = [
                    'order_id' => intval($order_id),
                    'customer_email' => $customer->email,
                    'value' => round((float)$order->total, 2)
                ];
                $res = $this->do_payment($data);
                if($res['status']){
                    $order->paid = 1;
                    $order->save();
                    return response()->json(['message' => "Order has been paid successfully!"]);
                }
                else{
                    return response()->json(['error' => $res['message']], 400);
                }
            }
            else{
                return response()->json(['error' => "Customer does not exist!"], 400);
            }
        }
        else{
            return response()->json(['error' => "Order does not exist!"], 400);
        }
    }

    private function do_payment($data){
        try{
            $headers['Content-Type'] = 'application/json';
            $url = Config::get('services.payment.url');
            $response = Http::withHeaders($headers);
            $response = $response->put($url, json_encode($data));
            if($response->ok()){
                return [
                    'status' => true
                ];
            }
            if($response->failed() || $response->clientError() || $response->serverError()){
                return [
                    'message' => $response->body(),
                    'status' => false
                ];
            }
        }
        catch(Exception $e){
            Log::info($e->getMessage());
            return [
                'message' => $e->getMessage(),
                'status' => false
            ];
        }
    }
}

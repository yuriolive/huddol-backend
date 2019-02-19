<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Order;
use App\OrderProduct;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return OrderResource::collection(Order::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // {"restaurant_id": 1, "products": [{"product_id":1, "quantity": 1}, {"product_id":2,"quantity": 2}]}
        $order_encoded = $request->json()->all();

        $validator1 = Validator::make($order_encoded, [
            'restaurant_id' => 'required|integer|exists:restaurants,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|integer|distinct',
            'products.*.quantity' => 'required|integer|gte:1',
        ]);
     
        if ($validator1->fails()) {
            return response()->json(['errors' => $validator1->errors()]);
        } else {
            $restaurant_id = $order_encoded['restaurant_id'];

            $validator2 = Validator::make($order_encoded, [
                'products.*.product_id' => 'exists:products,id,restaurant_id,' . $restaurant_id
            ]);

            if ($validator2->fails()) {
                return response()->json(['errors' => $validator2->errors()]);
            } else {
                // valid data
                DB::transaction(function () use ($restaurant_id, $order_encoded) {
                    $order_id = DB::table('orders')->insertGetId(['restaurant_id' => $restaurant_id]);
                    DB::table('order_products')->insert(array_map(function ($product) use ($order_id)
                    {
                        $product['order_id'] = $order_id;
                        return $product;
                    }, $order_encoded['products']));
                }, 5);
            }
        }

        return response()->json(['message' => 'Successfully submitted the order']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // TODO
        return new OrderResource(Order::find($id));
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
        // TODO
        // $validated = $request->validated();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // TODO If dont exist error
        $order = Order::find($id);
        $order->delete();

        return response()->json(null, 204);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::all();
        return view();
    }


    public function store(StoreOrderRequest $request)
    {
        $validatedData = $request->validated();
        $user = User::where('telegram_id', $validatedData['telegram_id'])->first();
        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $validatedData['total_price'],
            'status' => 'pending',
        ]);

        $order->items()->createMany($request->input('items'));

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order,
        ], 201);
    }
}

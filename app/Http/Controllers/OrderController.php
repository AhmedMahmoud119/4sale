<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Meal;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;

class OrderController extends Controller
{

    public function getPriceAfterDiscount($meal) {
        $discount = $meal->discount ?? 0;
        $priceAfterDiscount = $meal->price - ($meal->price * ($discount / 100));

        return $priceAfterDiscount;
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'reservation_id' => 'required|exists:reservations,id',
            'customer_id' => 'required|exists:customers,id',
            'user_id' => 'required|exists:users,id', // assuming you have a users table for waiters
            'meals' => 'required|array',
            'meals.*.id' => 'required|exists:meals,id',
            'meals.*.quantity' => 'required|integer|min:1'
        ]);

        $tableId = $request->input('table_id');
        $reservationId = $request->input('reservation_id');
        $customerId = $request->input('customer_id');
        $userId = $request->input('user_id');
        $meals = $request->input('meals');
        $orderDate = Carbon::now();

        $totalAmount = 0;

        // Calculate total amount with discounts
        foreach ($meals as $mealData) {
            $meal = Meal::find($mealData['id']);
            $quantity = $mealData['quantity'];
            $totalAmount += $this->getPriceAfterDiscount($meal) * $quantity;
        }

        // Create the order
        $order = Order::create([
            'table_id' => $tableId,
            'reservation_id' => $reservationId,
            'customer_id' => $customerId,
            'user_id' => $userId,
            'total' => $totalAmount,
            'paid' => false,
            'date' => $orderDate
        ]);

        // Create order details
        foreach ($meals as $mealData) {
            $meal = Meal::find($mealData['id']);
            $quantity = $mealData['quantity'];

            $amountToPay = $this->getPriceAfterDiscount($meal) * $quantity;

            OrderDetail::create([
                'order_id' => $order->id,
                'meal_id' => $meal->id,
                'amount_to_pay' => $amountToPay
            ]);
        }

        return response()->json(['order' => $order, 'order_details' => $order->orderDetails], 201);
    }
}

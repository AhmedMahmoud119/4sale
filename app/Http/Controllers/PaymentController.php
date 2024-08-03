<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Strategies\CheckoutStrategy;

class PaymentController extends Controller
{
    private $checkoutStrategy;

    public function __construct(CheckoutStrategy $checkoutStrategy)
    {
        $this->checkoutStrategy = $checkoutStrategy;
    }

    public function pay(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $orderId = $request->input('order_id');

        $order = Order::with('orderDetails.meal')->find($orderId);

        if ($order->paid) {
            return response()->json(['message' => 'Order is already paid'], 400);
        }

        $totalAmount = $this->checkoutStrategy->calculateTotal($order->total);

        $order->paid = true;
        $order->total = $totalAmount;
        $order->save();

        $invoice = $this->generateInvoice($order);

        return response()->json(['message' => 'Payment successful', 'invoice' => $invoice], 200);
    }

    private function generateInvoice($order)
    {
        $invoice = [];
        $invoice['order_id'] = $order->id;
        $invoice['table_id'] = $order->table_id;
        $invoice['reservation_id'] = $order->reservation_id;
        $invoice['customer_id'] = $order->customer_id;
        $invoice['user_id'] = $order->user_id;
        $invoice['date'] = $order->date;
        $invoice['total'] = $order->total;
        $invoice['details'] = [];

        foreach ($order->orderDetails as $detail) {
            $invoice['details'][] = [
                'meal_id' => $detail->meal_id,
                'meal_description' => $detail->meal->description,
                'amount_to_pay' => $detail->amount_to_pay,
            ];
        }

        return $invoice;
    }
}



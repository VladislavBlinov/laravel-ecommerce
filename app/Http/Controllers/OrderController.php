<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use YooKassa\Client;

use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Notification\NotificationWaitingForCapture;
use YooKassa\Model\NotificationEventType;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.product')
            ->where('user_id', auth()->id())
            ->get();
        return OrderResource::collection($orders);
    }

    public function show(string $id)
    {
        $order = Order::with('items.product')->find($id);
        return OrderResource::make($order);
    }

    public function create(request $request)
    {
        $total = 0;
        $products = [];
        foreach ($request->input('items') as $item) {
            $product = Product::find($item['product_id']);
            $product['quantity'] = $item['quantity'];
            $total += $product['price'] * $item['quantity'];
            $products[] = $product;
        }

        $order = Order::create([
            'user_id' => auth()->id(),
            'address' => $request->input('address'),
            'total' => $total,
        ]);

        foreach ($products as $product) {
            OrderItem::create([
                'order_id' => $order['id'],
                'product_id' => $product->id,
                'quantity' => $product->quantity,
                'price' => $product->price,
            ]);
        }


        $client = new Client();
        $client->setAuth(1092565, 'test_3m7Dm1o0JxSlwrHs9tazIZgA5z3woxoySSBqWvlvPfI');
        $payment = $client->createPayment(
            array(
                'amount' => array(
                    'value' => $total,
                    'currency' => 'RUB',
                ),
                'confirmation' => array(
                    'type' => 'redirect',
                    'return_url' => 'https://laravel-ecommerce.loc/orders/latest',
                ),
                'metadata' => [
                    'order_id' => $order['id'],
                ],
                'capture' => true,
                'description' => "Заказ №{$order->id}",
            ),
            uniqid('', true)
        );
        Log::info($payment->getId());

        $order->update([
            'payment_id' => $payment->getId(),
        ]);


        return response()->json([
            'status' => 'success',
            'order' => $order,
            'url' => $payment->getConfirmation()->getConfirmationUrl(),
        ], 200);

    }

    public function callback(Request $request)
    {
        $source = file_get_contents('php://input');
        $requestBody = json_decode($source, true);

        Log::info($requestBody);

        if (isset($requestBody['event'])) {
            $order = Order::find((int)$requestBody['object']['metadata']['order_id'])
                ->where('payment_id', $requestBody['object']['id'])
                ->first();
            if (!$order) {
                Log::error("Order {$requestBody['object']['metadata']['order_id']} not found");
                return response();
            }
            if ($requestBody['object']['status'] === 'succeeded' && $requestBody['object']['paid']) {

                $order->update([
                    'status' => 'Оплачен',
                ]);

            } else {
                $order->update([
                    'status' => 'Отменен',
                ]);
            }
        }
        return response();
    }

    public function latest()
    {
        $order = Order::where('user_id', auth()->id())->latest()->first();

        return response()->json($order);
    }
}

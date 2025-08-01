<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use YooKassa\Client;
use YooKassa\Model\NotificationEventType;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.product')
            ->where('user_id', auth()->id());
        return OrderResource::collection($orders->paginate(10));
    }

    public function show(string $orderId)
    {
        $order = Order::with('items.product')->findOrFail($orderId);
        if ($order->user_id != auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return OrderResource::make($order);
    }

    public function create(CreateOrderRequest $request)
    {
        $total = 0;
        $items = [];
        $productIds = collect($request->input('items'))->pluck('product_id');
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($request->input('items') as $item) {
            $product = $products[$item['product_id']];
            $quantity = $item['quantity'];
            $total += $product->price * $quantity;
            $items[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
            ];
        }

        return DB::transaction(function () use ($request, $total, $items) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'address' => $request->input('address'),
                'total' => $total,
            ]);

            foreach ($items as &$item) {
                $item['order_id'] = $order->id;
            }
            unset($item);

            OrderItem::insert($items);

            $client = new Client();
            $client->setAuth(config('services.yookassa.shop_id'), config('services.yookassa.secret_key'));
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

            $order->update([
                'payment_id' => $payment->getId(),
            ]);

            return response()->json([
                'status' => 'success',
                'order' => $order,
                'url' => $payment->getConfirmation()->getConfirmationUrl(),
            ]);
        });
    }

    public function callback()
    {
        $source = file_get_contents('php://input');
        $requestBody = json_decode($source, true);

        Log::info($requestBody);

        if (isset($requestBody['event'])) {
            $order = Order::where('payment_id', $requestBody['object']['id'])
                ->findOrFail((int)$requestBody['object']['metadata']['order_id']);
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
        $order = Order::with('items.product')->where('user_id', auth()->id())->latest()->first();

        return OrderResource::make($order);
    }
}

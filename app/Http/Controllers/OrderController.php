<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Support\Facades\Log;
use YooKassa\Model\NotificationEventType;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    )
    {
    }

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
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return OrderResource::make($order);
    }

    public function create(CreateOrderRequest $request)
    {
        try {
            $order = $this->orderService->create($request->validated());
            return response()->json($order);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    public function callback()
    {
        try {
            $this->orderService->callback();
            return response();
        } catch (\Exception $exception) {
            Log::error('Order callback error: ' . $exception->getMessage());
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    public function latest()
    {
        $order = Order::with('items.product')
            ->where('user_id', auth()->id())
            ->latest()
            ->first();

        return OrderResource::make($order);
    }
}

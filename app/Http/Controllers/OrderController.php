<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Traits\JsonResponseTrait;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class OrderController extends Controller implements HasMiddleware
{
    use JsonResponseTrait;
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show']),
        ];
    }
    public function index()
    {
        try {
            $orders = Order::all();
            return $this->jsonResponse(200, 'Success', $orders);
        } catch (\Exception $e) {
            \Log::error('Fetching orders failed', ['error' => $e->getMessage()]);
            return $this->jsonResponse(500, 'Failed');
        }
    }

    public function store(Request $request)
    {
        try {
            \Log::info('Store order method called', ['request' => $request->all()]);

            $fields = $request->validate([
                'items' => 'required|array',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'comments' => 'nullable|string',
            ]);

            // Create the order
            $order = Order::create([
                'user_id' => $request->user()->id,
                'total_price' => 0,
                'comments' => $fields['comments'] ?? null,
            ]);

            $totalPrice = 0;

            foreach ($fields['items'] as $item) {
                $product = Product::find($item['product_id']);
                $quantity = $item['quantity'];
                $price = $product->price * $quantity;
                $totalPrice += $price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'name' => $product->name,
                    'attributes' => $product->size . ' / ' . $product->color,
                    'total' => $price,
                ]);
            }

            // Update total price of the order
            $order->update(['total_price' => $totalPrice]);

            return $this->jsonResponse(200, 'Success', $order->load('items'));
        } catch (\Exception $e) {
            \Log::error('Order creation failed', ['error' => $e->getMessage()]);
            return $this->jsonResponse(500, 'Failed');
        }
    }

    public function show(Order $order)
    {
        return $this->jsonResponse(200, 'Success', $order->load('items'));
    }

    public function update(Request $request, Order $order)
    {
        try {
            Gate::authorize('modify', $order); // Only the creator can update

            $fields = $request->validate([
                'items' => 'required|array',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'comments' => 'nullable|string',
            ]);

            $totalPrice = 0;

            $order->items()->delete(); // Reset items

            foreach ($fields['items'] as $item) {
                $product = Product::find($item['product_id']);
                $quantity = $item['quantity'];
                $price = $product->price * $quantity;

                $totalPrice += $price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'name' => $product->name,
                    'attributes' => $product->size . ' / ' . $product->color,
                    'total' => $price,
                ]);
            }

            $order->update([
                'total_price' => $totalPrice,
                'comments' => $fields['comments'] ?? $order->comments,
            ]);

            return $this->jsonResponse(200, 'Success', $order->load('items'));
        } catch (\Exception $e) {
            \Log::error('Order update failed', ['error' => $e->getMessage()]);
            return $this->jsonResponse(500, 'Failed');
        }
    }

    public function destroy(Order $order)
    {
        try {
            Gate::authorize('modify', $order); // Only the creator can delete
            $order->delete();

            return $this->jsonResponse(200, 'Order deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Order deletion failed', ['error' => $e->getMessage()]);
            return $this->jsonResponse(500, 'Failed');
        }
    }
}

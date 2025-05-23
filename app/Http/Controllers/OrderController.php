<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['orderItems.product'])
            ->latest()
            ->paginate(10);
        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::with('discount')->get();
        return view('orders.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card'
        ]);

        $order = new Order();
        $order->payment_method = $validated['payment_method'];
        $order->total_amount = 0;
        $order->discount_amount = 0;
        $order->final_amount = 0;
        $order->save();

        $total = 0;
        $discount = 0;

        foreach ($validated['items'] as $item) {
            $product = Product::with('discount')->findOrFail($item['product_id']);

            if ($product->stock < $item['quantity']) {
                $order->delete();
                return back()->with('error', "Stok {$product->name} tidak mencukupi.");
            }

            $price = $product->price;
            $subtotal = $price * $item['quantity'];
            $itemDiscount = 0;

            // Check for discount
            if ($product->discount) {
                $discountModel = $product->discount;
                $itemDiscount = $subtotal * ($discountModel->percentage / 100);
                $subtotal -= $itemDiscount;
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $price,
                'subtotal' => $subtotal
            ]);

            // Update product stock
            $product->stock -= $item['quantity'];
            $product->save();

            $total += $price * $item['quantity'];
            $discount += $itemDiscount;
        }

        $order->total_amount = $total;
        $order->discount_amount = $discount;
        $order->final_amount = $total - $discount;
        $order->save();

        return redirect()->route('orders.receipt', $order)
            ->with('success', 'Pesanan berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function receipt(Order $order)
    {
        $order->load('orderItems.product');

        $receipt = "=== KASIR SYAFIRA ===\n";
        $receipt .= "Tanggal: " . $order->created_at->format('d/m/Y H:i:s') . "\n";
        $receipt .= "------------------------\n";

        foreach ($order->orderItems as $item) {
            $receipt .= "{$item->product->name}\n";
            $receipt .= "{$item->quantity} x Rp " . number_format($item->price, 0, ',', '.') . "\n";
            $receipt .= "Subtotal: Rp " . number_format($item->subtotal, 0, ',', '.') . "\n";
            $receipt .= "------------------------\n";
        }

        $receipt .= "Total: Rp " . number_format($order->total_amount, 0, ',', '.') . "\n";
        if ($order->discount_amount > 0) {
            $receipt .= "Diskon: Rp " . number_format($order->discount_amount, 0, ',', '.') . "\n";
        }
        $receipt .= "Final: Rp " . number_format($order->final_amount, 0, ',', '.') . "\n";
        $receipt .= "------------------------\n";
        $receipt .= "Metode Pembayaran: " . ucfirst($order->payment_method) . "\n";
        $receipt .= "=== TERIMA KASIH ===\n";

        return view('orders.receipt', compact('order', 'receipt'));
    }
}

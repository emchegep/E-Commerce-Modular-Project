<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Modules\Order\Http\Requests\CheckoutRequest;
use Modules\Order\Models\Order;
use Modules\Payment\PayBuddy;
use Modules\Product\Models\Product;
use RuntimeException;

class CheckoutController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function __invoke(CheckoutRequest $request)
    {
        $products= collect($request->input('products'))
            ->map(function ($productDetails) {
            return [
                'product' => Product::find($productDetails['id']),
                'quantity' => $productDetails['quantity'],
            ];
        });

        $orderTotalInCents = $products->sum(function ($productDetails) {
            return $productDetails['product']->price_in_cents * $productDetails['quantity'];
        });

        $payBuddy = PayBuddy::make();

        try {
            $charge = $payBuddy->charge(
                $request->payment_token,
                $orderTotalInCents,
                'Modularization'
            );
        } catch (RuntimeException) {
            throw ValidationException::withMessages([
                'payment_token' => 'We could not process your payment.',
            ]);
        }

        $order = Order::create([
            'user_id' => $request->user()->id,
            'total_in_cents' => $orderTotalInCents,
            'status' => 'paid',
            'payment_gateway' => 'PayBuddy',
            'payment_id' => $charge['id'],
        ]);

        $order->lines()->createMany($products->map(function ($productDetails) {
            $productDetails['product']->decrement('stock');
            return [
                'product_id' => $productDetails['product']->id,
                'quantity' => $productDetails['quantity'],
                'product_price_in_cents' =>
                    $productDetails['product']->price_in_cents,
            ];
        }));

        return response()->json([], 201);
    }
}

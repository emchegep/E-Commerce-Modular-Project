<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Modules\Order\Http\Requests\CheckoutRequest;
use Modules\Order\Models\Order;
use Modules\Payment\PayBuddy;
use Modules\Product\CartItemCollection;
use Modules\Product\Warehouse\ProductStockManager;
use RuntimeException;

class CheckoutController extends Controller
{
    public function __construct(
        protected ProductStockManager $productStockManager,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function __invoke(CheckoutRequest $request)
    {
        $cartItems = CartItemCollection::fromCheckoutData($request->input('products'));
        $orderTotalInCents = $cartItems->totalInCents();

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
            'status' => 'completed',
            'payment_gateway' => 'PayBuddy',
            'payment_id' => $charge['id'],
        ]);

        foreach ($cartItems->items() as $cartItem) {
            $this->productStockManager->decrement($cartItem->product->id, $cartItem->quantity);

            $order->lines()->create([
                'product_id' => $cartItem->product->id,
                'quantity' => $cartItem->quantity,
                'product_price_in_cents' => $cartItem->product->priceInCents,
            ]);
        }

        $payment = $order->payments()->create([
            'user_id' => $request->user()->id,
            'total_in_cents' => $orderTotalInCents,
            'status' => 'paid',
            'payment_gateway' => 'PayBuddy',
            'payment_id' => $charge['id'],
        ]);

        return response()->json([], 201);
    }
}

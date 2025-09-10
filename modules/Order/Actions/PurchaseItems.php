<?php

namespace Modules\Order\Actions;

use Illuminate\Database\DatabaseManager;
use Modules\Order\Models\Order;
use Modules\Payment\Actions\CreatePaymentForOrder;
use Modules\Payment\PayBuddy;
use Modules\Product\CartItemCollection;
use Modules\Product\Warehouse\ProductStockManager;

class PurchaseItems
{
    public function __construct(
        protected ProductStockManager $productStockManager,
        protected CreatePaymentForOrder $createPaymentForOrder,
        protected DatabaseManager $databaseManager
    ) {}

    public function handle(
        CartItemCollection $items,
        PayBuddy $paymentProvider,
        string $paymentToken,
        int $userId
    ): Order {
        $orderTotalInCents = $items->totalInCents();

        return $this->databaseManager->transaction(function () use (
            $items,
            $userId,
            $orderTotalInCents,
            $paymentProvider,
            $paymentToken
        ) {
            $order = Order::query()->create([
                'user_id' => $userId,
                'total_in_cents' => $orderTotalInCents,
                'status' => 'completed',
            ]);

            foreach ($items->items() as $cartItem) {
                $this->productStockManager->decrement($cartItem->product->id, $cartItem->quantity);

                $order->lines()->create([
                    'product_id' => $cartItem->product->id,
                    'quantity' => $cartItem->quantity,
                    'product_price_in_cents' => $cartItem->product->priceInCents,
                ]);
            }
            $this->createPaymentForOrder->handle(
                $order->id,
                $userId,
                $orderTotalInCents,
                $paymentProvider,
                $paymentToken
            );

            return $order;
        });
    }
}

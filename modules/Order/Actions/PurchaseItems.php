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

            $order = Order::startForUser($userId);
            $order->addLinesFromCartItems($items);
            $order->fulfill();

            foreach ($items->items() as $cartItem) {
                $this->productStockManager->decrement($cartItem->product->id, $cartItem->quantity);
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

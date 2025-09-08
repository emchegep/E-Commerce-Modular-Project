<?php

namespace Modules\Product;

use Illuminate\Support\Collection;
use Modules\Product\Models\Product;

class CartItemCollection
{
    /**
     * @param Collection<CartItem> $items
     */
    public function __construct(
        public Collection $items,
    )
    {

    }

    public static function fromCheckoutData(array $data): CartItemCollection
    {
        $cartItems = collect($data)
            ->map(function ($productDetails) {
                return new CartItem(
                    Product::find($productDetails['id']),
                    $productDetails['quantity']
                );
            });

        return new self($cartItems);
    }

    public function totalInCents()
    {
        return $this->items->sum(fn(CartItem $cartItem) =>
            $cartItem->product->price_in_cents * $cartItem->quantity);
    }

    /**
     * @return Collection<CartItem>
     */
    public function items(): Collection
    {
        return $this->items;
    }
}

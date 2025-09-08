<?php

namespace Modules\Product;

use Modules\Product\Models\Product;

readonly class CartItem
{
    public function __construct(
        public Product $product,
        public int $quantity,
    )
    {

    }
}

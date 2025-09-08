<?php

namespace Modules\Product\Warehouse;

use Modules\Product\Models\Product;

class ProductStockManager
{
    public function decrement(int $productId, int $quantity): void
    {
        Product::query()->find($productId)?->decrement('stock', $quantity);
    }
}

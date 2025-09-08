<?php

namespace Modules\Product\Tests\Models;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use JetBrains\PhpStorm\NoReturn;
use Modules\Product\Database\Factories\ProductFactory;
use Modules\Product\Models\Product;
use Modules\Product\Tests\ProductTestCase;

class ProductTest extends ProductTestCase
{
    use DatabaseMigrations;
    #[NoReturn]
    public function test_it_creates_a_product()
    {
        $product = Product::factory()->create();

        $this->assertTrue(true);
    }
}

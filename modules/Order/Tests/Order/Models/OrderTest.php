<?php

namespace Modules\Order\Tests\Order\Models;


use JetBrains\PhpStorm\NoReturn;
use Modules\Order\Models\Order;
use Modules\Order\Tests\OrderTestCase;

class OrderTest extends OrderTestCase
{
    #[NoReturn]
    public function test_it_creates_an_order(): void
    {
        $order = new Order();

        $this->assertTrue(true);
    }
}

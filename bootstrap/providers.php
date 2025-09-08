<?php

return [
    App\Providers\AppServiceProvider::class,
    \Modules\Payment\Infrastruture\Providers\PaymentServiceProvider::class,
    Modules\Order\Providers\OrderServiceProvider::class,
    Modules\Product\Providers\ProductServiceProvider::class,
    Modules\Shipment\Providers\ShipmentServiceProvider::class,
];

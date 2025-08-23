<?php

return [
    App\Providers\AppServiceProvider::class,
    \Modules\Shipment\Providers\ShipmentServiceProvider::class,
    Modules\Order\Providers\OrderServiceProvider::class,
    Modules\Product\Providers\ProductServiceProvider::class,
];

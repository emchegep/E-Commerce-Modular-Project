<?php

namespace Modules\Shipment\Tests\Models;

use Modules\Shipment\Models\Shipment;
use Modules\Shipment\Tests\ShipmentTestCase;

class ShipmentTest extends ShipmentTestCase
{
    public function test_it_creates_shipment()
    {
        $shipment = new Shipment;

        $this->assertInstanceOf(Shipment::class, $shipment);
    }
}

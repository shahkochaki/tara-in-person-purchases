<?php

namespace Shahkochaki\TaraService\Tests;

use PHPUnit\Framework\TestCase;
use Shahkochaki\TaraService\TaraService;
use Shahkochaki\TaraService\TaraConstants;
use Shahkochaki\TaraService\TaraException;

class TaraServiceTest extends TestCase
{
    public function test_tara_service_can_be_instantiated()
    {
        $service = new TaraService('1403', 'test_user', 'test_pass');

        $this->assertInstanceOf(TaraService::class, $service);
    }

    public function test_constants_exist()
    {
        $this->assertEquals(5, TaraConstants::UNIT_PIECE);
        $this->assertEquals(1, TaraConstants::UNIT_KILOGRAM);
        $this->assertEquals(1, TaraConstants::MADE_IRANIAN);
        $this->assertEquals(2, TaraConstants::MADE_FOREIGN);
    }

    public function test_helper_methods_work()
    {
        $service = new TaraService('1403');

        $payment = $service->createTracePayment(123456789, 50000, 0);
        $this->assertIsArray($payment);
        $this->assertEquals(123456789, $payment['barcode']);
        $this->assertEquals(50000, $payment['amount']);

        $item = $service->createPurchaseItem(
            'Test Item',
            'TEST001',
            TaraConstants::UNIT_PIECE,
            2,
            25000,
            'GRP1',
            'Test Group',
            TaraConstants::MADE_IRANIAN
        );

        $this->assertIsArray($item);
        $this->assertEquals('Test Item', $item['name']);
        $this->assertEquals(25000, $item['salePrice']);
    }
}

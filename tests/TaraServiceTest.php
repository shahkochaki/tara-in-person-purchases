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
        // Test with custom config to avoid missing credentials error
        $config = [
            'credentials' => [
                'username' => 'test_user',
                'password' => 'test_pass'
            ],
            'default_branch_code' => 'BRANCH_CODE'
        ];

        $service = new TaraService('BRANCH_CODE', $config);

        $this->assertInstanceOf(TaraService::class, $service);
    }

    public function test_constructor_requires_credentials()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Required credentials');

        // This should throw an exception because no credentials are provided
        new TaraService();
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
        // Use config to provide credentials
        $config = [
            'credentials' => ['username' => 'test', 'password' => 'test'],
            'default_branch_code' => 'BRANCH_CODE'
        ];

        $service = new TaraService('BRANCH_CODE', $config);

        // Test createTracePayment
        $payment = $service->createTracePayment(123456789, 50000, 0);
        $this->assertIsArray($payment);
        $this->assertEquals(123456789, $payment['barcode']);
        $this->assertEquals(50000, $payment['amount']);
        $this->assertEquals(0, $payment['data']);

        // Test createPurchaseItem
        $item = $service->createPurchaseItem(
            'Test Item',
            'TEST001',
            2.0,
            TaraConstants::UNIT_PIECE,
            25000,
            'GRP1',
            'Test Group',
            TaraConstants::MADE_IRANIAN
        );

        $this->assertIsArray($item);
        $this->assertEquals('Test Item', $item['name']);
        $this->assertEquals('TEST001', $item['code']);
        $this->assertEquals(2.0, $item['count']);
        $this->assertEquals(25000, $item['fee']);

        // Test createInvoiceData
        $invoiceData = $service->createInvoiceData(
            100000,
            'INV-123',
            'Test invoice',
            9000,
            [$item]
        );

        $this->assertIsArray($invoiceData);
        $this->assertEquals(100000, $invoiceData['totalPrice']);
        $this->assertEquals('INV-123', $invoiceData['invoiceNumber']);
        $this->assertEquals(9000, $invoiceData['vat']);
        $this->assertCount(1, $invoiceData['items']);
    }

    public function test_terminal_management()
    {
        // Use config to provide credentials
        $config = [
            'credentials' => ['username' => 'test', 'password' => 'test'],
            'default_branch_code' => 'BRANCH_CODE'
        ];

        $service = new TaraService('BRANCH_CODE', $config);

        // Test empty terminals initially
        $this->assertEmpty($service->getTerminals());
        $this->assertNull($service->getSelectedTerminal());

        // Mock terminals data
        $mockTerminals = [
            [
                'accessCode' => 'mock_access_code_1',
                'merchantCode' => '3475',
                'terminalCode' => '3674',
                'terminalTitle' => 'صندوق 1'
            ],
            [
                'accessCode' => 'mock_access_code_2',
                'merchantCode' => '3475',
                'terminalCode' => '3675',
                'terminalTitle' => 'صندوق 2'
            ]
        ];

        // Simulate terminals being set (normally done by getAccessCode)
        $reflection = new \ReflectionClass($service);
        $terminalsProperty = $reflection->getProperty('terminals');
        $terminalsProperty->setAccessible(true);
        $terminalsProperty->setValue($service, $mockTerminals);

        // Test terminal selection
        $this->assertTrue($service->selectTerminal('3674'));
        $this->assertFalse($service->selectTerminal('9999')); // Non-existent terminal

        // Test selected terminal
        $selectedTerminal = $service->getSelectedTerminal();
        $this->assertNotNull($selectedTerminal);
        $this->assertEquals('3674', $selectedTerminal['terminalCode']);
        $this->assertEquals('صندوق 1', $selectedTerminal['terminalTitle']);

        // Test merchant code
        $this->assertEquals('3475', $service->getMerchantCode());
    }

    public function test_token_validation_methods()
    {
        $service = new TaraService('BRANCH_CODE');

        // Test token getters/setters
        $this->assertNull($service->getToken());

        $service->setToken('test_token');
        $this->assertEquals('test_token', $service->getToken());

        // Test branch code
        $this->assertEquals('BRANCH_CODE', $service->getBranchCode());

        $service->setBranchCode('1404');
        $this->assertEquals('1404', $service->getBranchCode());
    }

    public function test_configuration_management()
    {
        // Test with custom config
        $customConfig = [
            'base_url' => 'https://test.example.com/api/v1',
            'credentials' => [
                'username' => 'test_user',
                'password' => 'test_pass'
            ],
            'default_branch_code' => '1405',
            'token' => [
                'buffer_seconds' => 120
            ],
            'logging' => [
                'enabled' => false
            ]
        ];

        $service = new TaraService('1405', $customConfig);

        // Test configuration values
        $this->assertEquals('https://test.example.com/api/v1', $service->getBaseUrl());
        $this->assertEquals('1405', $service->getBranchCode());
        $this->assertEquals('test_user', $service->getConfig('credentials.username'));
        $this->assertEquals(120, $service->getConfig('token.buffer_seconds'));
        $this->assertFalse($service->isLoggingEnabled());

        // Test configuration updates
        $service->setConfig('logging.enabled', true);
        $this->assertTrue($service->isLoggingEnabled());

        // Test credentials update
        $service->updateCredentials('new_user', 'new_pass');
        $credentials = $service->getCredentials();
        $this->assertEquals('new_user', $credentials['username']);
        $this->assertEquals('***masked***', $credentials['password']);

        // Test base URL update
        $service->updateBaseUrl('https://new.example.com/api/v1');
        $this->assertEquals('https://new.example.com/api/v1', $service->getBaseUrl());
    }

    public function test_config_helper_methods()
    {
        $service = new TaraService();

        // Test timeout settings
        $timeouts = $service->getTimeoutSettings();
        $this->assertIsArray($timeouts);
        $this->assertArrayHasKey('connect', $timeouts);
        $this->assertArrayHasKey('request', $timeouts);

        // Test config with default values
        $this->assertEquals('info', $service->getConfig('logging.level', 'info'));
        $this->assertEquals('default_value', $service->getConfig('non.existent.key', 'default_value'));

        // Test full config retrieval
        $allConfig = $service->getConfig();
        $this->assertIsArray($allConfig);
    }
}

# Tara Service for Laravel

A Laravel package for in-person credit card purchases using Tara Club API (Iranian payment service).

## Installation

```bash
composer require shahkochaki/tara-service
```

## Configuration

1. Publish the config file:

```bash
php artisan vendor:publish --provider="Shahkochaki\TaraService\TaraServiceProvider"
```

2. Add your Tara credentials to `.env`:

```env
TARA_BASE_URL=https://stage.tara-club.ir/club/api/v1
TARA_USERNAME=your_username
TARA_PASSWORD=your_password
TARA_BRANCH_CODE=your_branch_code
```

## Usage

### Basic Usage

```php
use Shahkochaki\TaraService\TaraService;
use Shahkochaki\TaraService\TaraConstants;

// Using dependency injection (recommended)
$tara = app(TaraService::class);

// Or create manually
$tara = new TaraService('your_branch_code', 'username', 'password');
```

### Complete Purchase Flow

```php
// 1. Login (automatic with first API call)
$loginResult = $tara->login();

// 2. Get access code and terminals
$accessCodeResult = $tara->getAccessCode();
$terminalCode = $tara->getTerminalCodeFromResponse($accessCodeResult, 0);

// 3. Create payment trace
$payment = [
    $tara->createTracePayment($userId, 100000, 0) // User ID, amount, discount
];
$traceResult = $tara->purchaseTrace($payment, $terminalCode);
$traceNumber = $traceResult['data']['traceNumber'];

// 4. Create purchase request
$item = $tara->createPurchaseItem(
    'Product Name',
    'PRODUCT001',
    TaraConstants::UNIT_PIECE,
    1, // quantity
    100000, // price
    'GRP001',
    'Group Name',
    TaraConstants::MADE_IRANIAN
);

$invoice = $tara->createInvoiceData(100000, 'INV001', '', 0, [$item]);
$purchaseData = $tara->createPurchaseRequestData(100000, 'INV001', '', [$invoice]);

// 5. Submit purchase request
$requestResult = $tara->purchaseRequest($purchaseData, $traceNumber);

// 6. Verify purchase
$verifyResult = $tara->purchaseVerify($traceNumber);
```

### Available Methods

- `login()` - Authenticate with Tara API
- `getAccessCode()` - Get available terminals
- `getMerchandiseGroups()` - Get product categories
- `purchaseTrace()` - Create payment trace
- `purchaseRequest()` - Submit purchase request
- `purchaseVerify()` - Verify purchase
- `purchaseReverse()` - Reverse/cancel purchase
- `purchaseInquiry()` - Query purchase status

### Helper Methods

- `createTracePayment($userId, $amount, $discount)` - Create payment data
- `createPurchaseItem($name, $code, $unit, $quantity, $price, $groupCode, $groupName, $madeIn)` - Create item
- `createInvoiceData($totalAmount, $invoiceNumber, $description, $discount, $items)` - Create invoice
- `createPurchaseRequestData($totalAmount, $invoiceNumber, $description, $invoices)` - Create purchase data
- `getTerminalCodeFromResponse($response, $index)` - Extract terminal code
- `getAvailableTerminals($response)` - Get all terminals

### Constants

```php
// Units
TaraConstants::UNIT_PIECE        // عدد
TaraConstants::UNIT_KILOGRAM     // کیلوگرم
TaraConstants::UNIT_METER        // متر

// Origin
TaraConstants::MADE_IRANIAN      // ایرانی
TaraConstants::MADE_FOREIGN      // خارجی
TaraConstants::MADE_UNKNOWN      // نامشخص
```

## Error Handling

All methods return arrays with this structure:

```php
[
    'success' => true/false,
    'data' => [], // Response data on success
    'error' => '', // Error message on failure
    'status' => 200, // HTTP status code
    'body' => '' // Raw response body
]
```

## Requirements

- PHP >= 7.4
- Laravel >= 8.0

## License

MIT

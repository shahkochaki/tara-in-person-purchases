# Tara In-Person Purchases for Laravel

<div align="center">

![Tara360](https://img.shields.io/badge/Tara360-Payment%20Gateway-blue)
![Laravel](https://img.shields.io/badge/Laravel-8%2B-red)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple)
![License](https://img.shields.io/badge/License-MIT-green)

**A Laravel package specifically designed for Tara360 in-person purchase integration - handles credit payments for physical stores and POS terminals**

[Installation](#installation) • [Configuration](#configuration) • [Usage](#usage) • [API Reference](#api-reference) • [فارسی](#فارسی)

</div>

## About Tara360

**Tara360** is Iran's leading credit payment and digital wallet application that enables customers to make purchases on credit and pay in installments. With over **14,000+ merchant partners** across Iran, Tara360 provides:

### � Specialized for In-Person Purchases

- **Physical Store Integration**: Designed specifically for brick-and-mortar stores
- **POS Terminal Support**: Works with Tara360 POS terminals and cash registers
- **Branch & Terminal Management**: Handle multiple store locations and terminals
- **Real-time Transaction Processing**: Immediate payment processing for in-store purchases

### 💳 Credit Amounts

- **Micro Credit**: 500,000 to 10,000,000 IRR
- **Quick Loan**: Additional loan facilities for approved users
- **Organization Services**: Corporate credit solutions

### 🏆 Awards & Recognition

- **Winner of FAB Silver Award** in IT Product Excellence at the 8th National IT Awards
- Trusted by thousands of merchants and millions of customers

---

> **Note**: This package is specifically designed for **in-person purchases** at physical stores using Tara360 POS terminals. For online purchases or other Tara services, please check our other packages.

## Installation

### Option 1: Install from Packagist (Recommended)

```bash
composer require shahkochaki/tara-in-person-purchases
```

### Option 2: Install from GitHub (If Packagist is not available)

Add to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/shahkochaki/tara-in-person-purchases.git"
        }
    ],
    "require": {
        "shahkochaki/tara-in-person-purchases": "^1.0"
    }
}
```

Then run:
```bash
composer install
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
TARA_LOGGING=true
```

3. (Optional) Configure in `config/tara.php`:

```php
return [
    'base_url' => env('TARA_BASE_URL', 'https://stage.tara-club.ir/club/api/v1'),
    'username' => env('TARA_USERNAME', ''),
    'password' => env('TARA_PASSWORD', ''),
    'branch_code' => env('TARA_BRANCH_CODE', ''),
    'logging' => env('TARA_LOGGING', true),
];
```

## Usage

### Basic Implementation

```php
use Shahkochaki\TaraService\TaraService;
use Shahkochaki\TaraService\TaraConstants;

// Using dependency injection (recommended)
$tara = app(TaraService::class);

// Or create manually
$tara = new TaraService('branch_code', 'username', 'password');
```

### Complete Purchase Flow

```php
try {
    // 1. Login (automatic with first API call)
    $loginResult = $tara->login();

    if (!$loginResult['success']) {
        throw new Exception('Login failed: ' . $loginResult['error']);
    }

    // 2. Get access code and select terminal
    $accessCodeResult = $tara->getAccessCode();
    $terminalCode = $tara->getTerminalCodeFromResponse($accessCodeResult, 0);

    // 3. Create payment trace for customer
    $userId = 1234567890; // Customer's user ID
    $amount = 500000; // Amount in IRR (Rials)

    $payment = [
        $tara->createTracePayment($userId, $amount, 0) // userId, amount, discount
    ];

    $traceResult = $tara->purchaseTrace($payment, $terminalCode);

    if (!$traceResult['success']) {
        throw new Exception('Trace failed: ' . $traceResult['error']);
    }

    $traceNumber = $traceResult['data']['traceNumber'];

    // 4. Create purchase items and invoice
    $item = $tara->createPurchaseItem(
        'Premium Product',           // Product name
        'PROD001',                  // Product code
        TaraConstants::UNIT_PIECE,  // Unit type
        2,                          // Quantity
        250000,                     // Unit price (IRR)
        'ELECTRONICS',              // Group code
        'Electronics',              // Group name
        TaraConstants::MADE_FOREIGN // Origin
    );

    $invoice = $tara->createInvoiceData(
        $amount,      // Total amount
        'INV-2025001', // Invoice number
        'Purchase description',
        0,            // Discount
        [$item]       // Items array
    );

    $purchaseData = $tara->createPurchaseRequestData(
        $amount,       // Total amount
        'INV-2025001', // Invoice number
        'Purchase via Tara API',
        [$invoice]     // Invoices array
    );

    // 5. Submit purchase request
    $requestResult = $tara->purchaseRequest($purchaseData, $traceNumber);

    if (!$requestResult['success']) {
        throw new Exception('Purchase request failed: ' . $requestResult['error']);
    }

    // 6. Verify purchase
    $verifyResult = $tara->purchaseVerify($traceNumber);

    if ($verifyResult['success']) {
        echo "Purchase completed successfully!";
        // Process successful payment
    } else {
        echo "Purchase verification failed: " . $verifyResult['error'];
        // Handle failed verification
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    // Handle errors appropriately
}
```

## API Reference

### Core Methods

#### Authentication

```php
$result = $tara->login();
// Returns: ['success' => bool, 'data' => array, 'error' => string]
```

#### Terminal Management

```php
$result = $tara->getAccessCode($branchCode = null);
$terminals = $tara->getAvailableTerminals($accessCodeResult);
$terminalCode = $tara->getTerminalCodeFromResponse($accessCodeResult, $index);
```

#### Product Categories

```php
$result = $tara->getMerchandiseGroups();
```

#### Purchase Flow

```php
// Create payment trace
$result = $tara->purchaseTrace($paymentArray, $terminalCode);

// Submit purchase request
$result = $tara->purchaseRequest($purchaseData, $traceNumber);

// Verify purchase
$result = $tara->purchaseVerify($traceNumber);

// Reverse/cancel purchase
$result = $tara->purchaseReverse($traceNumber);

// Inquiry purchase status
$result = $tara->purchaseInquiry($referenceOrTraceNumber);
```

### Helper Methods

```php
// Create payment data for trace
$payment = $tara->createTracePayment($userId, $amount, $discount);

// Create purchase item
$item = $tara->createPurchaseItem($name, $code, $unit, $quantity, $price, $groupCode, $groupName, $madeIn);

// Create invoice data
$invoice = $tara->createInvoiceData($totalAmount, $invoiceNumber, $description, $discount, $items);

// Create complete purchase request data
$purchaseData = $tara->createPurchaseRequestData($totalAmount, $invoiceNumber, $description, $invoices);
```

### Constants

```php
// Product Units
TaraConstants::UNIT_PIECE        // 5 - Piece
TaraConstants::UNIT_KILOGRAM     // 1 - Kilogram
TaraConstants::UNIT_METER        // 2 - Meter
TaraConstants::UNIT_LITER        // 3 - Liter
TaraConstants::UNIT_GRAM         // 4 - Gram

// Product Origin
TaraConstants::MADE_IRANIAN      // 1 - Made in Iran
TaraConstants::MADE_FOREIGN      // 2 - Foreign made
TaraConstants::MADE_UNKNOWN      // 0 - Unknown origin
```

## Response Format

All methods return a standardized response array:

```php
[
    'success' => true|false,    // Operation success status
    'data' => [],              // Response data on success
    'error' => '',             // Error message on failure
    'status' => 200,           // HTTP status code
    'body' => ''               // Raw response body
]
```

## Error Handling

```php
use Shahkochaki\TaraService\TaraException;

try {
    $result = $tara->purchaseRequest($data, $traceNumber);

    if (!$result['success']) {
        // Handle API errors
        Log::error('Tara API Error: ' . $result['error']);
        throw new TaraException($result['error']);
    }

} catch (TaraException $e) {
    // Handle Tara-specific exceptions
    return response()->json(['error' => $e->getMessage()], 400);
} catch (Exception $e) {
    // Handle general exceptions
    return response()->json(['error' => 'Payment processing failed'], 500);
}
```

## Testing

```bash
# Run tests
composer test

# Or with PHPUnit directly
./vendor/bin/phpunit
```

## Requirements

- **PHP**: >= 7.4
- **Laravel**: >= 8.0
- **Extensions**: cURL, JSON

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Security

If you discover any security vulnerabilities, please email [your-email@example.com] instead of using the issue tracker.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for detailed changes.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Support

- **Phone**: 1573 (7 days a week, 24 hours)
- **Website**: [https://tara360.ir](https://tara360.ir)
- **Documentation**: [https://tara360.ir/faq](https://tara360.ir/faq)

---

# فارسی

## درباره تارا۳۶۰

**تارا۳۶۰** یکی از پیشروترین اپلیکیشن‌های پرداخت اعتباری و کیف پول دیجیتال ایران است که به مشتریان امکان خرید اعتباری و پرداخت قسطی را می‌دهد. با بیش از **۱۴,۰۰۰ فروشگاه** شریک در سراسر ایران، تارا۳۶۰ خدماتی شامل:

### ✨ ویژگی‌های کلیدی

- **خرید اعتباری**: الان بخر، بعداً در ۲-۳ قسط پرداخت کن
- **کیف پول دیجیتال**: کیف پول نقدی با ۳٪ بازگشت پول
- **بدون ضامن**: تأیید آنی اعتبار بدون نیاز به ضامن
- **تأیید فوری**: دریافت اعتبار در کمتر از ۲۰ دقیقه
- **شبکه گسترده**: ۱۴,۰۰۰+ فروشگاه و مرکز خدماتی در سراسر کشور
- **آنلاین و حضوری**: پشتیبانی از خرید آنلاین و حضوری

### 💰 میزان اعتبار

- **اعتبار خرد**: ۵۰۰,۰۰۰ تا ۱۰,۰۰۰,۰۰۰ تومان
- **وام فوری**: امکانات وام اضافی برای کاربران تأیید شده
- **خدمات سازمانی**: راهکارهای اعتباری سازمانی

## نصب

```bash
composer require shahkochaki/tara-in-person-purchases
```

## تنظیمات

۱. انتشار فایل تنظیمات:

```bash
php artisan vendor:publish --provider="Shahkochaki\TaraService\TaraServiceProvider"
```

۲. اضافه کردن اطلاعات تارا به `.env`:

```env
TARA_BASE_URL=https://stage.tara-club.ir/club/api/v1
TARA_USERNAME=نام_کاربری_شما
TARA_PASSWORD=رمز_عبور_شما
TARA_BRANCH_CODE=کد_شعبه_شما
```

## استفاده

### پیاده‌سازی ساده

```php
use Shahkochaki\TaraService\TaraService;
use Shahkochaki\TaraService\TaraConstants;

// استفاده از dependency injection (توصیه شده)
$tara = app(TaraService::class);

// یا ایجاد دستی
$tara = new TaraService('کد_شعبه', 'نام_کاربری', 'رمز_عبور');
```

### فرآیند کامل خرید

```php
// ۱. ورود به سیستم (خودکار با اولین فراخوانی API)
$loginResult = $tara->login();

// ۲. دریافت کد دسترسی و انتخاب ترمینال
$accessCodeResult = $tara->getAccessCode();
$terminalCode = $tara->getTerminalCodeFromResponse($accessCodeResult, 0);

// ۳. ایجاد ردیابی پرداخت
$userId = ۱۲۳۴۵۶۷۸۹۰; // شناسه کاربری مشتری
$amount = ۵۰۰۰۰۰; // مبلغ به ریال

$payment = [
    $tara->createTracePayment($userId, $amount, 0)
];

$traceResult = $tara->purchaseTrace($payment, $terminalCode);
$traceNumber = $traceResult['data']['traceNumber'];

// ۴. ایجاد آیتم خرید و فاکتور
$item = $tara->createPurchaseItem(
    'محصول پریمیم',                // نام محصول
    'PROD001',                  // کد محصول
    TaraConstants::UNIT_PIECE,  // نوع واحد
    ۲,                          // تعداد
    ۲۵۰۰۰۰,                     // قیمت واحد (ریال)
    'ELECTRONICS',              // کد گروه
    'الکترونیک',                // نام گروه
    TaraConstants::MADE_FOREIGN // منشأ
);

// ۵. ارسال درخواست خرید
$requestResult = $tara->purchaseRequest($purchaseData, $traceNumber);

// ۶. تأیید خرید
$verifyResult = $tara->purchaseVerify($traceNumber);
```

## پشتیبانی

- **تلفن**: ۱۵۷۳ (۷ روز هفته، ۲۴ ساعته)
- **وب‌سایت**: [https://tara360.ir](https://tara360.ir)
- **سوالات متداول**: [https://tara360.ir/faq](https://tara360.ir/faq)

## مجوز

مجوز MIT. برای اطلاعات بیشتر [فایل مجوز](LICENSE) را مشاهده کنید.

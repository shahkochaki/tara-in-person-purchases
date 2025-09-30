# Tara In-Person Purchases API - Updated Guide

## فرآیند کامل API تارا برای خرید حضوری

این راهنما فرآیند کامل استفاده از API تارا برای پرداخت های حضوری را توضیح می‌دهد.

### مراحل فرآیند

#### 1. مرحله ورود (Login)

ابتدا با نام کاربری و رمز عبور وارد سیستم می‌شویم:

```php
$tara = new TaraService('BRANCH_CODE'); // کد شعبه
$loginResult = $tara->login();
```

**پاسخ شامل:**

- `accessCode`: توکن دسترسی اصلی
- `expiryDuration`: مدت اعتبار توکن (به میلی‌ثانیه)

#### 2. دریافت لیست صندوق‌ها (Get Access Codes)

با استفاده از توکن اصلی، لیست صندوق‌های موجود را دریافت می‌کنیم:

```php
$accessResult = $tara->getAccessCode('BRANCH_CODE');
```

**پاسخ شامل آرایه‌ای از صندوق‌ها با اطلاعات:**

- `accessCode`: توکن دسترسی مخصوص هر صندوق
- `merchantCode`: کد فروشنده
- `terminalCode`: کد صندوق
- `terminalTitle`: نام صندوق
- `identifier`: شناسه

#### 3. انتخاب صندوق

یکی از صندوق‌ها را برای تراکنش انتخاب می‌کنیم:

```php
$terminalCode = $accessResult['data'][0]['terminalCode'];
$tara->selectTerminal($terminalCode);
```

#### 4. ایجاد رد (Purchase Trace)

با استفاده از کد یکبار مصرف مشتری، رد تراکنش ایجاد می‌کنیم:

```php
$payment = [
    $tara->createTracePayment(
        9700083615425377, // بارکد یکبار مصرف مشتری
        100000,           // مبلغ (ریال)
        0                 // داده اضافی
    )
];

$traceResult = $tara->purchaseTrace($payment, $terminalCode);
$traceNumber = $traceResult['data']['traceNumber'];
```

**نکات مهم:**

- از `accessCode` مخصوص صندوق انتخابی استفاده می‌شود
- بارکد باید از مشتری دریافت شود
- `traceNumber` برای مراحل بعدی نیاز است

#### 5. ارسال درخواست خرید (Purchase Request)

اطلاعات فاکتور و کالاها را ارسال می‌کنیم:

```php
// ایجاد اقلام فاکتور
$items = [
    $tara->createPurchaseItem(
        'نان سنگک',  // نام کالا
        '12345',     // کد کالا
        2.0,         // تعداد
        5,           // واحد (5 = عدد)
        50000,       // قیمت واحد
        'BAKERY',    // گروه
        'نانوایی',   // عنوان گروه
        1            // ساخت (1 = ایرانی)
    )
];

// ایجاد داده فاکتور
$invoiceData = $tara->createInvoiceData(
    100000,           // قیمت کل
    'INV-' . time(),  // شماره فاکتور
    'خرید از نانوایی', // توضیحات
    9000,             // مالیات (9%)
    $items            // اقلام
);

// ارسال درخواست
$requestResult = $tara->purchaseRequest($purchaseData, $traceNumber);
```

#### 6. تایید نهایی (Purchase Verify)

تراکنش را نهایی می‌کنیم:

```php
$verifyResult = $tara->purchaseVerify($traceNumber);
```

### روش‌های راحت‌تر

#### استفاده از متد تکی برای راه‌اندازی کامل:

```php
$tara = new TaraService('BRANCH_CODE');
$sessionResult = $tara->initializeSession();
```

#### استفاده از فرآیند کامل خرید:

```php
$result = $tara->completePurchaseFlow($payment, $purchaseData, $terminalCode);
```

### مثال کامل

```php
use Shahkochaki\TaraService\TaraService;

// 1. ایجاد نمونه سرویس
$tara = new TaraService('BRANCH_CODE');

// 2. راه‌اندازی جلسه (ورود + دریافت صندوق‌ها)
$sessionResult = $tara->initializeSession();
if (!$sessionResult['success']) {
    die('خطا در راه‌اندازی: ' . $sessionResult['error']);
}

// 3. نمایش صندوق‌های موجود
echo "صندوق‌های موجود:\n";
foreach ($tara->getTerminals() as $terminal) {
    echo "- {$terminal['terminalTitle']} (کد: {$terminal['terminalCode']})\n";
}

// 4. انتخاب اولین صندوق
$terminalCode = $tara->getTerminals()[0]['terminalCode'];
$tara->selectTerminal($terminalCode);

// 5. آماده‌سازی داده‌های پرداخت
$payment = [
    $tara->createTracePayment(9700083615425377, 100000, 0)
];

// 6. آماده‌سازی داده‌های خرید
$items = [
    $tara->createPurchaseItem('نان سنگک', '12345', 2.0, 5, 50000, 'BAKERY', 'نانوایی', 1)
];

$invoiceData = $tara->createInvoiceData(100000, 'INV-' . time(), 'خرید', 9000, $items);
$purchaseData = $tara->createPurchaseRequestData(100000, 'INV-' . time(), 'تست', $invoiceData);

// 7. اجرای فرآیند کامل خرید
$result = $tara->completePurchaseFlow($payment, $purchaseData, $terminalCode);

if ($result['success']) {
    echo "خرید با موفقیت انجام شد!\n";
    echo "شماره رد: " . $result['traceNumber'] . "\n";
} else {
    echo "خطا در خرید: " . $result['error'] . "\n";
}
```

### تغییرات مهم در نسخه جدید

1. **مدیریت زمان انقضا توکن**: حالا سرویس زمان انقضا توکن را بررسی می‌کند
2. **ذخیره اطلاعات صندوق‌ها**: تمام اطلاعات صندوق‌ها ذخیره و مدیریت می‌شود
3. **استفاده از توکن صندوق**: در `purchaseTrace` از توکن مخصوص صندوق استفاده می‌شود
4. **متدهای راحت**: متدهای کمکی برای سهولت استفاده اضافه شده
5. **فرآیند کامل**: امکان اجرای کل فرآیند با یک فراخوانی

### نکات مهم

- **بارکد مشتری**: باید هر بار از مشتری دریافت شود (کد یکبار مصرف)
- **انتخاب صندوق**: اختیاری است، در صورت عدم انتخاب اولین صندوق استفاده می‌شود
- **مدیریت خطا**: همه متدها وضعیت موفقیت برمی‌گردانند
- **زمان انقضا**: توکن اصلی ۳۰ دقیقه اعتبار دارد

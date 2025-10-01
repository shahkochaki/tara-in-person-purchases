# 🎉 Tara In-Person Purchases v2.2.0 Released!

## 🚀 نسخه 2.2.0 - مدیریت خطاهای بهبود یافته

### ✨ ویژگی‌های جدید

- **🔧 مدیریت خطاهای استاندارد**: تمام متدهای API حالا خطاها را در فرمت یکسان بازمی‌گردانند
- **📋 فرمت جدید خطا**: `'error' => ['title' => '', 'code' => 0000, 'message' => '']`
- **🔍 متد parseErrorResponse()**: پردازش یکپارچه خطاهای API
- **📚 TaraErrorHandlingExample.php**: مثال‌های کامل مدیریت خطا
- **🇮🇷 پیام‌های فارسی**: پیام‌های خطای کاربرپسند با کدهای خطا
- **💡 سیستم تجزیه و تحلیل خطا**: دسته‌بندی خطاها با پیشنهادات عملی

### 🛠️ بهبودهای فنی

- **BREAKING CHANGE**: همه متدهای API حالا فرمت استاندارد خطا را برمی‌گردانند
- پشتیبانی از فرمت خطای API: `{"data":{"code":84780028,"message":"موجودی کافی نیست."},"success":false}`
- بهبود مدیریت خطا در تمام endpoint های API:
  - `login()`
  - `getAccessCode()`
  - `getMerchandiseGroups()`
  - `purchaseTrace()`
  - `purchaseRequest()`
  - `purchaseVerify()`
  - `purchaseReverse()`
  - `purchaseInquiry()`

### 📖 مستندات

- بخش جامع مدیریت خطا در README
- جدول مرجع کدهای خطای رایج
- مثال‌های عملی مدیریت خطا
- راهنمای troubleshooting

### 🎯 مثال استفاده

```php
$result = $tara->purchaseRequest($amount, $payerIdentity);

if (isset($result['error'])) {
    $error = $result['error'];

    echo "خطا: " . $error['title'] . "\n";
    echo "کد: " . $error['code'] . "\n";
    echo "پیام: " . $error['message'] . "\n";

    // مدیریت خطاهای خاص
    switch ($error['code']) {
        case 84780028:
            echo "راهکار: موجودی حساب را بررسی کنید\n";
            break;
        case 84780001:
            echo "راهکار: اطلاعات کاربری را بررسی کنید\n";
            break;
    }
}
```

### 📊 کدهای خطای رایج

| کد         | معنی                   | راهکار                      |
| ---------- | ---------------------- | --------------------------- |
| `84780028` | موجودی کافی نیست       | بررسی موجودی حساب           |
| `84780001` | اطلاعات کاربری نامعتبر | بررسی نام کاربری و رمز عبور |
| `84780002` | مبلغ تراکنش نامعتبر    | بررسی مبلغ وارد شده         |
| `84780003` | تراکنش تکراری          | بررسی تراکنش‌های قبلی       |
| `84780004` | خطا در اتصال           | بررسی اتصال شبکه            |

## 📦 نصب

```bash
composer require shahkochaki/tara-in-person-purchases:^2.2.0
```

## 🔗 لینک‌ها

- [GitHub Repository](https://github.com/shahkochaki/tara-in-person-purchases)
- [Packagist](https://packagist.org/packages/shahkochaki/tara-in-person-purchases)
- [Documentation](https://github.com/shahkochaki/tara-in-person-purchases#readme)

## 🙏 تشکر

از تمام کاربرانی که بازخورد ارسال کردند و به بهبود این پکیج کمک کردند، تشکر می‌کنیم.

---

**نسخه کامل**: v2.2.0  
**تاریخ انتشار**: 1 اکتبر 2025  
**سازگاری**: PHP 7.4+ | Laravel 8+

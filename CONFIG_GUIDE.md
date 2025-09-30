# راهنمای کانفیگ سرویس تارا

## نحوه استفاده از فایل کانفیگ

### 1. فایل کانفیگ اصلی (`config/tara.php`)

فایل کانفیگ اصلی شامل تمام تنظیمات سرویس تارا است:

```php
return [
    'base_url' => env('TARA_BASE_URL', 'https://stage.tara-club.ir/club/api/v1'),
    'credentials' => [
        'username' => env('TARA_USERNAME', 'cashdesk_sandbox'),
        'password' => env('TARA_PASSWORD', '1qaz@WSX'),
    ],
    'default_branch_code' => env('TARA_BRANCH_CODE'),
    // ... سایر تنظیمات
];
```

### 2. متغیرهای محیطی (`.env`)

برای امنیت بیشتر، اطلاعات حساس را در فایل `.env` قرار دهید:

```bash
TARA_BASE_URL=https://stage.tara-club.ir/club/api/v1
TARA_USERNAME=your_username
TARA_PASSWORD=your_password
TARA_BRANCH_CODE=your_branch
```

### 3. نحوه استفاده در کد

#### استفاده پایه:

```php
use Shahkochaki\TaraService\TaraService;

// استفاده از کانفیگ پیش‌فرض
$tara = new TaraService();
```

#### استفاده با کانفیگ سفارشی:

```php
$customConfig = [
    'base_url' => 'https://api.tara-club.ir/club/api/v1',
    'credentials' => [
        'username' => 'my_username',
        'password' => 'my_password'
    ]
];

$tara = new TaraService('BRANCH_CODE', $customConfig);
```

## تنظیمات موجود

### تنظیمات پایه

| کلید                   | توضیح           | مقدار پیش‌فرض                            |
| ---------------------- | --------------- | ---------------------------------------- |
| `base_url`             | آدرس پایه API   | `https://stage.tara-club.ir/club/api/v1` |
| `credentials.username` | نام کاربری      | `cashdesk_sandbox`                       |
| `credentials.password` | کلمه عبور       | `1qaz@WSX`                               |
| `default_branch_code`  | کد شعبه پیش‌فرض | `BRANCH_CODE`                            |

### تنظیمات توکن

| کلید                   | توضیح                          | مقدار پیش‌فرض |
| ---------------------- | ------------------------------ | ------------- |
| `token.buffer_seconds` | بافر قبل از انقضا توکن (ثانیه) | `60`          |
| `token.auto_refresh`   | تمدید خودکار توکن              | `true`        |

### تنظیمات زمان انقضا

| کلید              | توضیح                      | مقدار پیش‌فرض |
| ----------------- | -------------------------- | ------------- |
| `timeout.connect` | زمان انقضا اتصال (ثانیه)   | `30`          |
| `timeout.request` | زمان انقضا درخواست (ثانیه) | `60`          |

### تنظیمات لاگ

| کلید              | توضیح                 | مقدار پیش‌فرض |
| ----------------- | --------------------- | ------------- |
| `logging.enabled` | فعال/غیرفعال بودن لاگ | `true`        |
| `logging.level`   | سطح لاگ               | `info`        |
| `logging.channel` | کانال لاگ             | `default`     |

### تنظیمات تکرار درخواست

| کلید                 | توضیح                          | مقدار پیش‌فرض |
| -------------------- | ------------------------------ | ------------- |
| `retry.enabled`      | فعال/غیرفعال بودن تکرار        | `true`        |
| `retry.max_attempts` | حداکثر تعداد تلاش              | `3`           |
| `retry.delay_ms`     | تاخیر بین تلاش‌ها (میلی‌ثانیه) | `1000`        |

## متدهای مدیریت کانفیگ

### دریافت مقادیر کانفیگ:

```php
// دریافت کل کانفیگ
$allConfig = $tara->getConfig();

// دریافت یک مقدار خاص
$baseUrl = $tara->getConfig('base_url');

// دریافت با dot notation
$username = $tara->getConfig('credentials.username');

// دریافت با مقدار پیش‌فرض
$bufferTime = $tara->getConfig('token.buffer_seconds', 60);
```

### تنظیم مقادیر کانفیگ:

```php
// تنظیم یک مقدار
$tara->setConfig('logging.enabled', false);

// تنظیم با dot notation
$tara->setConfig('credentials.username', 'new_user');
```

### متدهای کمکی:

```php
// بروزرسانی اطلاعات احراز هویت
$tara->updateCredentials('new_username', 'new_password');

// بروزرسانی آدرس پایه
$tara->updateBaseUrl('https://api.tara-club.ir/club/api/v1');

// دریافت اطلاعات فعلی
$credentials = $tara->getCredentials(); // رمز عبور پنهان می‌شود
$baseUrl = $tara->getBaseUrl();
$timeouts = $tara->getTimeoutSettings();

// بررسی وضعیت لاگ
if ($tara->isLoggingEnabled()) {
    echo "Logging is enabled";
}
```

## محیط‌های مختلف

### محیط توسعه (Development):

```php
$devConfig = [
    'base_url' => 'https://dev.tara-club.ir/club/api/v1',
    'credentials' => [
        'username' => 'dev_user',
        'password' => 'dev_pass'
    ],
    'logging' => ['enabled' => true, 'level' => 'debug']
];
```

### محیط تست (Staging):

```php
$stagingConfig = [
    'base_url' => 'https://stage.tara-club.ir/club/api/v1',
    'credentials' => [
        'username' => 'stage_user',
        'password' => 'stage_pass'
    ],
    'logging' => ['enabled' => true, 'level' => 'info']
];
```

### محیط تولید (Production):

```php
$productionConfig = [
    'base_url' => 'https://api.tara-club.ir/club/api/v1',
    'credentials' => [
        'username' => 'prod_user',
        'password' => 'prod_pass'
    ],
    'logging' => ['enabled' => false]
];
```

## مثال کامل

```php
use Shahkochaki\TaraService\TaraService;

// 1. ایجاد نمونه با کانفیگ پیش‌فرض
$tara = new TaraService();

// 2. بررسی تنظیمات فعلی
echo "Base URL: " . $tara->getBaseUrl() . "\n";
echo "Branch Code: " . $tara->getBranchCode() . "\n";

// 3. بروزرسانی تنظیمات در صورت نیاز
if ($tara->getConfig('environment') === 'production') {
    $tara->setConfig('logging.enabled', false);
}

// 4. استفاده از سرویس
$sessionResult = $tara->initializeSession();
if ($sessionResult['success']) {
    echo "Session initialized successfully!\n";
}
```

## نکات امنیتی

1. **هرگز اطلاعات حساس را در کد hard-code نکنید**
2. **از متغیرهای محیطی یا فایل‌های کانفیگ محلی استفاده کنید**
3. **فایل `.env` را در `.gitignore` قرار دهید**
4. **برای محیط تولید از کانال‌های امن برای مدیریت کانفیگ استفاده کنید**

## مسیریابی خطا

اگر کانفیگ بارگذاری نشد:

1. مسیر فایل `config/tara.php` را بررسی کنید
2. دسترسی‌های فایل را چک کنید
3. syntax فایل PHP را بررسی کنید
4. در صورت لزوم کانفیگ را به صورت manual به constructor پاس دهید

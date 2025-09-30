# تنظیمات اولیه سرویس تارا

## ⚠️ نکته مهم امنیتی

**اطلاعات احراز هویت (نام کاربری، رمز عبور، کد شعبه) هرگز در کد hard-code نمی‌شوند. این اطلاعات باید از طریق متغیرهای محیطی یا فایل‌های کانفیگ محلی تنظیم شوند.**

## مراحل تنظیم

### 1. کپی کردن فایل .env

```bash
cp .env.example .env
```

### 2. تنظیم اطلاعات احراز هویت در .env

فایل `.env` را ویرایش کرده و اطلاعات واقعی خود را جایگزین کنید:

```bash
# اطلاعات احراز هویت - با اطلاعات واقعی خود جایگزین کنید
TARA_USERNAME=نام_کاربری_شما
TARA_PASSWORD=رمز_عبور_شما
TARA_BRANCH_CODE=کد_شعبه_شما

# آدرس API (اختیاری - پیش‌فرض محیط تست)
TARA_BASE_URL=https://stage.tara-club.ir/club/api/v1
```

### 3. استفاده در کد

پس از تنظیم متغیرهای محیطی، می‌توانید از سرویس استفاده کنید:

```php
use Shahkochaki\TaraService\TaraService;

try {
    // سرویس به طور خودکار اطلاعات را از متغیرهای محیطی دریافت می‌کند
    $tara = new TaraService();

    // یا با مشخص کردن کد شعبه
    $tara = new TaraService('کد_شعبه_شما');

    // راه‌اندازی جلسه
    $sessionResult = $tara->initializeSession();

    if ($sessionResult['success']) {
        echo "اتصال موفق!\n";
    }

} catch (Exception $e) {
    echo "خطا: " . $e->getMessage() . "\n";
    echo "لطفاً اطلاعات احراز هویت را در فایل .env تنظیم کنید\n";
}
```

## روش‌های مختلف تنظیم

### 1. از طریق متغیرهای محیطی (.env)

```bash
TARA_USERNAME=your_username
TARA_PASSWORD=your_password
TARA_BRANCH_CODE=your_branch_code
```

### 2. از طریق کانفیگ سفارشی

```php
$config = [
    'base_url' => 'https://api.tara-club.ir/club/api/v1',
    'credentials' => [
        'username' => 'your_username',
        'password' => 'your_password'
    ],
    'default_branch_code' => 'your_branch_code'
];

$tara = new TaraService('your_branch_code', $config);
```

### 3. بروزرسانی در زمان اجرا

```php
$tara = new TaraService();
$tara->updateCredentials('new_username', 'new_password');
$tara->updateBaseUrl('https://new-api.tara.com');
```

## محیط‌های مختلف

### محیط تست (Staging)

```bash
TARA_BASE_URL=https://stage.tara-club.ir/club/api/v1
TARA_ENVIRONMENT=staging
```

### محیط تولید (Production)

```bash
TARA_BASE_URL=https://api.tara-club.ir/club/api/v1
TARA_ENVIRONMENT=production
```

## تست اتصال

برای تست اتصال و اطمینان از صحت تنظیمات:

```php
use Shahkochaki\TaraService\TaraService;

try {
    $tara = new TaraService();

    echo "تنظیمات فعلی:\n";
    echo "Base URL: " . $tara->getBaseUrl() . "\n";
    echo "Branch Code: " . $tara->getBranchCode() . "\n";
    echo "Credentials: " . json_encode($tara->getCredentials()) . "\n";

    // تست ورود
    $loginResult = $tara->login();
    if ($loginResult['success']) {
        echo "✅ ورود موفق - اطلاعات احراز هویت صحیح است\n";
        echo "Token: " . substr($loginResult['token'], 0, 20) . "...\n";
    } else {
        echo "❌ ورود ناموفق: " . $loginResult['error'] . "\n";
    }

} catch (Exception $e) {
    echo "❌ خطا: " . $e->getMessage() . "\n";
}
```

## نکات امنیتی

1. **فایل `.env` را هرگز در git commit نکنید**
2. **در محیط تولید از روش‌های امن مدیریت متغیرهای محیطی استفاده کنید**
3. **رمزهای عبور را به صورت منظم تغییر دهید**
4. **دسترسی به فایل‌های کانفیگ را محدود کنید**

## مسیریابی خطا

### خطای "Required credentials are missing"

- مطمئن شوید فایل `.env` وجود دارد
- بررسی کنید متغیرهای `TARA_USERNAME`، `TARA_PASSWORD`، `TARA_BRANCH_CODE` تنظیم شده‌اند
- مطمئن شوید مقادیر خالی نیستند

### خطای "Login failed"

- نام کاربری و رمز عبور را بررسی کنید
- مطمئن شوید آدرس API صحیح است
- بررسی کنید اتصال اینترنت برقرار است

### خطای "Config file not found"

- مطمئن شوید فایل `config/tara.php` وجود دارد
- یا اطلاعات را به صورت manual به constructor پاس دهید

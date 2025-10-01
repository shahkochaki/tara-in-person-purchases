<?php

namespace Shahkochaki\TaraService;

use Shahkochaki\TaraService\TaraService;
use Shahkochaki\TaraService\TaraConstants;

/**
 * نمونه کد اصلاح شده برای فرآیند کامل خرید تارا
 * این کد مشکل purchaseData را حل می‌کند
 */
class TaraExampleFixed
{
    /**
     * فرآیند کامل خرید با تمام مراحل ضروری
     * 
     * @param array $data داده‌های ورودی
     * @param string $key کلید داده
     * @return array نتیجه عملیات
     */
    public function completePurchaseProcess(array $data, string $key): array
    {
        // فرض کنیم $data[$key] شامل: 'branchCode.userBarcode.amount' است
        $explodeRequest = explode('.', $data[$key]);
        $branchCode = $explodeRequest[0];     // کد شعبه
        $userBarcode = $explodeRequest[1];    // بارکد کاربر
        $amount = (int)$explodeRequest[2];    // مبلغ

        // 1. راه‌اندازی و ورود
        $tara = new TaraService($branchCode);
        $session = $tara->initializeSession();

        // بررسی نتیجه session
        if (!$session['success']) {
            return ['success' => false, 'error' => 'Session initialization failed', 'details' => $session];
        }

        // 2. انتخاب صندوق (اختیاری)
        $accessCodeResult = $tara->getAccessCode();
        if (!$accessCodeResult['success']) {
            return ['success' => false, 'error' => 'Failed to get access code', 'details' => $accessCodeResult];
        }

        $terminalCode = $tara->getTerminalCodeFromResponse($accessCodeResult, 0);
        if (!$terminalCode) {
            return ['success' => false, 'error' => 'No terminal available'];
        }

        // 3. آماده‌سازی پرداخت
        $payment = [$tara->createTracePayment($userBarcode, $amount, 0)];

        // 4. ایجاد آیتم‌های خرید (این قسمت کم بود!)
        $item = $tara->createPurchaseItem(
            'محصول نمونه',                    // نام محصول
            'PROD001',                      // کد محصول
            1.0,                           // تعداد
            TaraConstants::UNIT_PIECE,     // واحد (عدد)
            $amount,                       // قیمت
            'GENERAL',                     // کد گروه
            'عمومی',                       // عنوان گروه
            TaraConstants::MADE_IRANIAN,   // ساخت ایران
            ''                             // اطلاعات اضافی
        );

        // 5. ایجاد اطلاعات فاکتور
        $invoiceData = $tara->createInvoiceData(
            $amount,                       // مبلغ کل
            'INV' . time(),               // شماره فاکتور
            '',                           // اطلاعات اضافی
            (int)($amount * 0.09),        // مالیات (9 درصد)
            [$item]                       // آیتم‌های خرید
        );

        // 6. ایجاد داده‌های درخواست خرید (purchaseData)
        $purchaseData = $tara->createPurchaseRequestData(
            $amount,                      // مبلغ
            'INV' . time(),              // شماره فاکتور
            '',                          // اطلاعات اضافی
            $invoiceData               // اطلاعات فاکتور
        );

        // 7. فرآیند کامل خرید
        $result = $tara->completePurchaseFlow($payment, $purchaseData, $terminalCode);

        return [
            'success' => $result['success'],
            'session' => $session,
            'terminalCode' => $terminalCode,
            'payment' => $payment,
            'purchaseData' => $purchaseData,  // اضافه شد
            'result' => $result,
        ];
    }

    /**
     * نمونه ساده‌تر با پارامترهای جداگانه
     */
    public function simplePurchaseProcess(string $branchCode, string $userBarcode, int $amount): array
    {
        $tara = new TaraService($branchCode);

        // راه‌اندازی
        $session = $tara->initializeSession();
        if (!$session['success']) {
            return $session;
        }

        // دریافت ترمینال
        $accessCodeResult = $tara->getAccessCode();
        if (!$accessCodeResult['success']) {
            return $accessCodeResult;
        }

        $terminalCode = $tara->getTerminalCodeFromResponse($accessCodeResult, 0);
        if (!$terminalCode) {
            return ['success' => false, 'error' => 'No terminal available'];
        }

        // آماده‌سازی پرداخت
        $payment = [$tara->createTracePayment($userBarcode, $amount, 0)];

        // ایجاد محصول
        $item = $tara->createPurchaseItem(
            'محصول',
            'PROD' . time(),
            1.0,
            TaraConstants::UNIT_PIECE,
            $amount,
            'GENERAL',
            'عمومی',
            TaraConstants::MADE_IRANIAN
        );

        // ایجاد فاکتور
        $invoiceData = $tara->createInvoiceData(
            $amount,
            'INV' . time(),
            '',
            0, // بدون مالیات
            [$item]
        );

        // ایجاد درخواست خرید
        $purchaseData = $tara->createPurchaseRequestData(
            $amount,
            'INV' . time(),
            '',
            [$invoiceData]
        );

        // اجرای فرآیند
        return $tara->completePurchaseFlow($payment, $purchaseData, $terminalCode);
    }
}

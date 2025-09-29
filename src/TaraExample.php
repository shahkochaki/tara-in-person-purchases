<?php

namespace Shahkochaki\TaraService;

use Illuminate\Support\Facades\Log;

/**
 * نمونه استفاده از سرویس Tara
 */
class TaraExample
{
    public function exampleUsage($barCode = null)
    {
        // ایجاد نمونه سرویس
        $taraService = new TaraService('1403');

        // ورود به سیستم - توکن از accessCode دریافت می‌شود
        $loginResult = $taraService->login();
        if (!$loginResult['success']) {
            return $loginResult;
        }

        // دریافت کد دسترسی - لیست ترمینال‌ها
        $accessCodeResult = $taraService->getAccessCode();
        if (!$accessCodeResult['success']) {
            return $accessCodeResult;
        }

        // انتخاب ترمینال (روش ساده با متد کمکی)
        $terminalCode = $taraService->getTerminalCodeFromResponse($accessCodeResult, 0); // صندوق اول
        if (!$terminalCode) {
            return ['success' => false, 'error' => 'No terminal available'];
        }

        // یا دریافت لیست کامل ترمینال‌ها برای انتخاب دستی
        // $terminals = $taraService->getAvailableTerminals($accessCodeResult);
        // $terminalCode = $terminals[0]['terminalCode'];

        // دریافت گروه‌های کالا
        $merchandiseGroupsResult = $taraService->getMerchandiseGroups();
        if (!$merchandiseGroupsResult['success']) {
            return $merchandiseGroupsResult;
        }

        // ایجاد داده‌های پرداخت برای trace
        $paymentData = [
            $taraService->createTracePayment($barCode, 100_000, 0) // barcode is user ID
        ];

        // trace خرید با terminal code واقعی
        $traceResult = $taraService->purchaseTrace($paymentData, $terminalCode);
        if (!$traceResult['success']) {
            return $traceResult;
        }

        // دریافت trace number از پاسخ trace
        $traceNumber = $traceResult['data']['traceNumber'] ?? 'SAMPLE_TRACE_123';

        // ایجاد آیتم‌های خرید
        $item1 = $taraService->createPurchaseItem(
            'نام کالا',
            'کد کالا',
            1.0,
            5, // عدد
            10000,
            'گروه',
            'عنوان گروه',
            1, // ایرانی
            ''
        );

        // ایجاد داده‌های فاکتور
        $invoiceData = $taraService->createInvoiceData(
            10000,
            'INV001',
            '',
            1000, // مالیات
            [$item1]
        );

        // ایجاد درخواست خرید کامل
        $purchaseRequestData = $taraService->createPurchaseRequestData(
            10000,
            'INV001',
            '',
            [$invoiceData]
        );

        // ارسال درخواست خرید با trace number
        $purchaseResult = $taraService->purchaseRequest($purchaseRequestData, $traceNumber);
        if (!$purchaseResult['success']) {
            return $purchaseResult;
        }

        // تایید خرید با trace number
        $verifyResult = $taraService->purchaseVerify($traceNumber);
        if (!$verifyResult['success']) {
            return $verifyResult;
        }

        return "Purchase completed successfully!";
    }

    public function exampleWithErrorHandling()
    {
        try {
            $taraService = new TaraService();

            // ورود
            $result = $taraService->login();
            if (!$result['success']) {
                throw new \Exception('Login failed: ' . $result['error']);
            }

            // سایر عملیات...

        } catch (\Exception $e) {
            Log::error('Tara Service Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}

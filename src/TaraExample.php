<?php

namespace Shahkochaki\TaraService;

use Illuminate\Support\Facades\Log;

/**
 * Example usage of Tara service
 */
class TaraExample
{
    public function exampleUsage($branchCode = null, $barCode = null)
    {
        // Create service instance
        $taraService = new TaraService($branchCode);

        // Login to system - token is retrieved from accessCode field
        $loginResult = $taraService->login();
        if (!$loginResult['success']) {
            return $loginResult;
        }

        // Get access code - terminals list
        $accessCodeResult = $taraService->getAccessCode();
        if (!$accessCodeResult['success']) {
            return $accessCodeResult;
        }

        // Select terminal (simple method with helper)
        $terminalCode = $taraService->getTerminalCodeFromResponse($accessCodeResult, 0); // First terminal
        if (!$terminalCode) {
            return ['success' => false, 'error' => 'No terminal available'];
        }

        // Alternative: Get complete terminals list for manual selection
        // $terminals = $taraService->getAvailableTerminals($accessCodeResult);
        // $terminalCode = $terminals[0]['terminalCode'];

        // Get merchandise groups
        $merchandiseGroupsResult = $taraService->getMerchandiseGroups();
        if (!$merchandiseGroupsResult['success']) {
            return $merchandiseGroupsResult;
        }

        // Create payment data for trace
        $paymentData = [
            $taraService->createTracePayment($barCode, 100_000, 0) // barcode is user ID
        ];

        // Trace purchase with actual terminal code
        $traceResult = $taraService->purchaseTrace($paymentData, $terminalCode);
        if (!$traceResult['success']) {
            return $traceResult;
        }

        // Get trace number from trace response
        $traceNumber = $traceResult['data']['traceNumber'] ?? 'SAMPLE_TRACE_123';

        // Create purchase items
        $item1 = $taraService->createPurchaseItem(
            'Product Name',
            'Product Code',
            1.0,
            5, // Piece
            10000,
            'Group',
            'Group Title',
            1, // Iranian
            ''
        );

        // Create invoice data
        $invoiceData = $taraService->createInvoiceData(
            10000,
            'INV001',
            '',
            1000, // Tax
            [$item1]
        );

        // Create complete purchase request
        $purchaseRequestData = $taraService->createPurchaseRequestData(
            10000,
            'INV001',
            '',
            $invoiceData
        );

        // Send purchase request with trace number
        $purchaseResult = $taraService->purchaseRequest($purchaseRequestData, $traceNumber);
        if (!$purchaseResult['success']) {
            return $purchaseResult;
        }

        // Verify purchase with trace number
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

            // Login
            $result = $taraService->login();
            if (!$result['success']) {
                throw new \Exception('Login failed: ' . $result['error']);
            }

            // Further operations...
        } catch (\Exception $e) {
            Log::error('Tara Service Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}

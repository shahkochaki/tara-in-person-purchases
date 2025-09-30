<?php

namespace Shahkochaki\TaraService;

use Exception;

/**
 * Updated example showing the correct API flow based on the new requirements
 * IMPORTANT: Set your credentials in .env file before running these examples
 */
class TaraExampleUpdated
{
    public function example()
    {
        try {
            // IMPORTANT: Make sure to set your credentials in .env file
            $tara = new TaraService('your_branch_code'); // Replace with your branch code

            // Step 1: Initialize session (login + get terminals)
            echo "Step 1: Initializing session...\n";
            $sessionResult = $tara->initializeSession();

            if (!$sessionResult['success']) {
                echo "Session initialization failed: " . $sessionResult['error'] . "\n";
                return;
            }

            echo "Session initialized successfully!\n";
            echo "Available terminals:\n";
            foreach ($tara->getTerminals() as $terminal) {
                echo "- Terminal {$terminal['terminalCode']}: {$terminal['terminalTitle']}\n";
            }

            // Step 2: Select a terminal (optional - if not selected, first one will be used)
            $terminalCode = $tara->getTerminals()[0]['terminalCode']; // Use first terminal
            if ($tara->selectTerminal($terminalCode)) {
                echo "Selected terminal: {$terminalCode}\n";
            }

            // Step 3: Create payment data for purchase trace
            $payment = [
                $tara->createTracePayment(
                    9700083615425377, // Customer barcode (one-time use code from customer)
                    100000, // Amount in Rials
                    0 // Additional data
                )
            ];

            // Step 4: Perform purchase trace to get trace number
            echo "\nStep 4: Performing purchase trace...\n";
            $traceResult = $tara->purchaseTrace($payment, $terminalCode);

            if (!$traceResult['success']) {
                echo "Purchase trace failed: " . $traceResult['error'] . "\n";
                return;
            }

            $traceNumber = $traceResult['data']['traceNumber'];
            echo "Purchase trace successful! Trace number: {$traceNumber}\n";
            echo "Customer mobile: " . $traceResult['data']['mobile'] . "\n";

            // Step 5: Create invoice items
            $items = [
                $tara->createPurchaseItem(
                    'نان سنگک', // Product name
                    '12345', // Product code
                    2.0, // Count
                    5, // Unit (5 = piece)
                    50000, // Fee per unit
                    'BAKERY', // Group
                    'نانوایی', // Group title
                    1 // Made (1 = Iranian)
                )
            ];

            // Step 6: Create invoice data
            $invoiceData = $tara->createInvoiceData(
                100000, // Total price
                'INV-' . time(), // Invoice number
                'Purchase from bakery', // Data
                9000, // VAT (9%)
                $items
            );

            // Step 7: Create purchase request data
            $purchaseData = $tara->createPurchaseRequestData(
                100000, // Amount
                'INV-' . time(), // Invoice number
                'Test purchase', // Data
                $invoiceData
            );

            // Step 8: Send purchase request
            echo "\nStep 8: Sending purchase request...\n";
            $requestResult = $tara->purchaseRequest($purchaseData, $traceNumber);

            if (!$requestResult['success']) {
                echo "Purchase request failed: " . $requestResult['error'] . "\n";
                return;
            }

            echo "Purchase request successful!\n";

            // Step 9: Verify purchase
            echo "\nStep 9: Verifying purchase...\n";
            $verifyResult = $tara->purchaseVerify($traceNumber);

            if ($verifyResult['success']) {
                echo "Purchase verified successfully!\n";
                echo "Final status: " . json_encode($verifyResult['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
            } else {
                echo "Purchase verification failed: " . $verifyResult['error'] . "\n";
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            echo "Please ensure you have set TARA_USERNAME, TARA_PASSWORD, and TARA_BRANCH_CODE in your .env file\n";
        }
    }

    /**
     * Example of using the complete purchase flow method
     */
    public function exampleCompleteFlow()
    {
        try {
            $tara = new TaraService('your_branch_code'); // Replace with your branch code

            // Initialize session
            $sessionResult = $tara->initializeSession();
            if (!$sessionResult['success']) {
                echo "Session initialization failed\n";
                return;
            }

            // Prepare payment data (replace with actual customer barcode)
            $payment = [
                $tara->createTracePayment(9700083615425377, 100000, 0) // Replace barcode with actual customer code
            ];

            // Prepare purchase data
            $items = [
                $tara->createPurchaseItem('نان سنگک', '12345', 2.0, 5, 50000, 'BAKERY', 'نانوایی', 1)
            ];

            $invoiceData = $tara->createInvoiceData(100000, 'INV-' . time(), 'Purchase', 9000, $items);
            $purchaseData = $tara->createPurchaseRequestData(100000, 'INV-' . time(), 'Test', $invoiceData);

            // Execute complete flow
            $result = $tara->completePurchaseFlow($payment, $purchaseData);

            if ($result['success']) {
                echo "Complete purchase flow successful!\n";
                echo "Trace Number: " . $result['traceNumber'] . "\n";
            } else {
                echo "Purchase flow failed: " . $result['error'] . "\n";
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            echo "Please ensure credentials are properly configured\n";
        }
    }

    /**
     * Example of handling different terminals
     */
    public function exampleTerminalSelection()
    {
        try {
            $tara = new TaraService('your_branch_code'); // Replace with your branch code

            // Initialize and get terminals
            $sessionResult = $tara->initializeSession();
            if (!$sessionResult['success']) {
                return;
            }

            $terminals = $tara->getTerminals();
            echo "Available terminals:\n";

            foreach ($terminals as $index => $terminal) {
                echo "{$index}: {$terminal['terminalTitle']} (Code: {$terminal['terminalCode']})\n";
            }

            // Select specific terminal
            if (count($terminals) > 1) {
                $selectedTerminal = $terminals[1]; // Select second terminal
                if ($tara->selectTerminal($selectedTerminal['terminalCode'])) {
                    echo "Selected: " . $selectedTerminal['terminalTitle'] . "\n";

                    // Get merchant code for this terminal
                    echo "Merchant Code: " . $tara->getMerchantCode() . "\n";
                }
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            echo "Please ensure credentials are properly configured\n";
        }
    }
}

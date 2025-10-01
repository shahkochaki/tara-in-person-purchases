<?php

/**
 * Example class demonstrating error handling with TaraService
 * Shows how to properly handle API errors and display user-friendly messages
 * 
 * This class demonstrates the new error format:
 * 'error' => ['title' => '', 'code' => 0000, 'message' => '']
 */
class TaraErrorHandlingExample
{
    /**
     * Display user-friendly error messages
     */
    public function displayUserFriendlyError($error)
    {
        $userFriendlyMessages = [
            84780028 => 'موجودی حساب کافی نیست. لطفاً موجودی خود را بررسی کنید.',
            84780001 => 'اطلاعات کاربری نامعتبر است.',
            84780002 => 'مبلغ تراکنش نامعتبر است.',
            84780003 => 'تراکنش قبلاً انجام شده است.',
            84780004 => 'خطا در اتصال به سرور',
        ];

        $friendlyMessage = isset($userFriendlyMessages[$error['code']])
            ? $userFriendlyMessages[$error['code']]
            : $error['message'];

        return [
            'success' => false,
            'error' => [
                'title' => $error['title'],
                'code' => $error['code'],
                'message' => $friendlyMessage,
                'original_message' => $error['message']
            ]
        ];
    }

    /**
     * Analyze error type and provide suggestions
     */
    public function analyzeError($error)
    {
        $suggestions = [];

        switch ($error['code']) {
            case 84780028:
                $suggestions[] = 'موجودی حساب را بررسی کنید';
                $suggestions[] = 'از مبلغ کمتری استفاده کنید';
                break;
            case 84780001:
                $suggestions[] = 'نام کاربری و رمز عبور را بررسی کنید';
                $suggestions[] = 'با پشتیبانی تماس بگیرید';
                break;
            case 84780002:
                $suggestions[] = 'مبلغ تراکنش باید بیشتر از صفر باشد';
                $suggestions[] = 'حداکثر مبلغ مجاز را بررسی کنید';
                break;
            default:
                $suggestions[] = 'لطفاً دوباره تلاش کنید';
                $suggestions[] = 'در صورت تکرار خطا با پشتیبانی تماس بگیرید';
        }

        return [
            'error_analysis' => [
                'severity' => $this->getErrorSeverity($error['code']),
                'category' => $this->getErrorCategory($error['code']),
                'suggestions' => $suggestions
            ]
        ];
    }

    /**
     * Get error severity level
     */
    private function getErrorSeverity($code)
    {
        $criticalErrors = [84780001, 84780004];
        $warningErrors = [84780028, 84780002];

        if (in_array($code, $criticalErrors)) {
            return 'critical';
        } elseif (in_array($code, $warningErrors)) {
            return 'warning';
        }

        return 'info';
    }

    /**
     * Get error category
     */
    private function getErrorCategory($code)
    {
        $categories = [
            84780028 => 'insufficient_balance',
            84780001 => 'authentication',
            84780002 => 'validation',
            84780003 => 'duplicate_transaction',
            84780004 => 'connection'
        ];

        return isset($categories[$code]) ? $categories[$code] : 'unknown';
    }

    /**
     * Test different error scenarios
     */
    public function testErrorScenarios()
    {
        echo "Testing Tara Error Handling\n";
        echo "===========================\n\n";

        // Test insufficient balance error
        $this->testInsufficientBalance();

        // Test authentication error
        $this->testAuthenticationError();

        // Test validation error
        $this->testValidationError();
    }

    /**
     * Test insufficient balance scenario
     */
    private function testInsufficientBalance()
    {
        echo "Test 1: Insufficient Balance\n";
        echo "-----------------------------\n";

        // Mock insufficient balance error
        $mockError = [
            'title' => 'Transaction Error',
            'code' => 84780028,
            'message' => 'موجودی کافی نیست.'
        ];

        $result = $this->displayUserFriendlyError($mockError);
        $analysis = $this->analyzeError($mockError);

        echo "Error: " . json_encode($result, JSON_UNESCAPED_UNICODE) . "\n";
        echo "Analysis: " . json_encode($analysis, JSON_UNESCAPED_UNICODE) . "\n\n";
    }

    /**
     * Test authentication error scenario
     */
    private function testAuthenticationError()
    {
        echo "Test 2: Authentication Error\n";
        echo "-----------------------------\n";

        // Mock authentication error
        $mockError = [
            'title' => 'Authentication Error',
            'code' => 84780001,
            'message' => 'نام کاربری یا رمز عبور اشتباه است.'
        ];

        $result = $this->displayUserFriendlyError($mockError);
        $analysis = $this->analyzeError($mockError);

        echo "Error: " . json_encode($result, JSON_UNESCAPED_UNICODE) . "\n";
        echo "Analysis: " . json_encode($analysis, JSON_UNESCAPED_UNICODE) . "\n\n";
    }

    /**
     * Test validation error scenario
     */
    private function testValidationError()
    {
        echo "Test 3: Validation Error\n";
        echo "-------------------------\n";

        // Mock validation error
        $mockError = [
            'title' => 'Validation Error',
            'code' => 84780002,
            'message' => 'مبلغ تراکنش نامعتبر است.'
        ];

        $result = $this->displayUserFriendlyError($mockError);
        $analysis = $this->analyzeError($mockError);

        echo "Error: " . json_encode($result, JSON_UNESCAPED_UNICODE) . "\n";
        echo "Analysis: " . json_encode($analysis, JSON_UNESCAPED_UNICODE) . "\n\n";
    }

    /**
     * Example usage of the error handling system
     */
    public function exampleUsage()
    {
        echo "Example: Error Format Display\n";
        echo "=============================\n\n";

        // Show how errors are formatted
        echo "All errors are now returned in this standard format:\n";
        echo "'error' => ['title' => '', 'code' => 0000, 'message' => '']\n\n";
    }
}

// Example usage
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    $example = new TaraErrorHandlingExample();

    echo "Tara Service Error Handling Examples\n";
    echo "=====================================\n\n";

    // Run error scenario tests
    $example->testErrorScenarios();

    // Show example usage
    $example->exampleUsage();
}

<?php

namespace Shahkochaki\TaraService;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TaraService
{
    private string $baseUrl;
    private string $username;
    private string $password;
    private ?string $token = null;
    private string $branchCode;

    public function __construct(string $branchCode = '1403')
    {
        $this->baseUrl = 'https://stage.tara-club.ir/club/api/v1';
        $this->username = 'cashdesk_sandbox';
        $this->password = '1qaz@WSX';
        $this->branchCode = $branchCode;
    }

    /**
     * Login to Tara API and get access token
     */
    public function login(): array
    {
        try {
            $response = Http::post("{$this->baseUrl}/user/login/merchant", [
                'principal' => $this->username,
                'password' => $this->password
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->token = $data['accessCode'] ?? null;

                return [
                    'success' => true,
                    'data' => $data,
                    'token' => $this->token
                ];
            }

            return [
                'success' => false,
                'error' => 'Login failed',
                'status' => $response->status(),
                'body' => $response->body()
            ];
        } catch (Exception $e) {
            Log::error('Tara Login Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get access code for branch
     */
    public function getAccessCode(string $branchCode = null): array
    {
        $branchCode = $branchCode ?? $this->branchCode;

        if (!$this->token) {
            $loginResult = $this->login();
            if (!$loginResult['success']) {
                return $loginResult;
            }
        }

        try {
            $response = Http::withToken($this->token)
                ->post("{$this->baseUrl}/merchant/access/code/{$branchCode}", [
                    'branchCode' => $branchCode
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Access code request failed',
                'status' => $response->status(),
                'body' => json_decode($response->getContents(), true) ?? $response->body()
            ];
        } catch (Exception $e) {
            Log::error('Tara Access Code Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get merchandise groups
     */
    public function getMerchandiseGroups(): array
    {
        if (!$this->token) {
            $loginResult = $this->login();
            if (!$loginResult['success']) {
                return $loginResult;
            }
        }

        try {
            $response = Http::withToken($this->token)
                ->post("{$this->baseUrl}/purchase/merchandise/groups");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Merchandise groups request failed',
                'status' => $response->status(),
                'body' => $response->body()
            ];
        } catch (Exception $e) {
            Log::error('Tara Merchandise Groups Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Purchase trace
     * 
     * @param array $payment Payment array with barcode (user ID), amount, data
     * @param string $terminalCode Terminal code
     */
    public function purchaseTrace(array $payment, string $terminalCode = null): array
    {
        $terminalCode = $terminalCode ?? $this->branchCode;

        if (!$this->token) {
            $loginResult = $this->login();
            if (!$loginResult['success']) {
                return $loginResult;
            }
        }

        try {
            $response = Http::withToken($this->token)
                ->post("{$this->baseUrl}/purchase/trace/{$terminalCode}", [
                    'terminalCode' => $terminalCode,
                    'payment' => $payment
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Purchase trace failed',
                'status' => $response->status(),
                'body' => $response->body()
            ];
        } catch (Exception $e) {
            Log::error('Tara Purchase Trace Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Purchase request
     * 
     * @param array $purchaseData Purchase request data
     * @param string $traceNumber Trace number from purchase trace
     */
    public function purchaseRequest(array $purchaseData, string $traceNumber): array
    {
        if (!$this->token) {
            $loginResult = $this->login();
            if (!$loginResult['success']) {
                return $loginResult;
            }
        }

        try {
            // اضافه کردن traceNumber به body
            $purchaseData['traceNumber'] = $traceNumber;

            $response = Http::withToken($this->token)
                ->post("{$this->baseUrl}/purchase/request/{$traceNumber}", $purchaseData);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Purchase request failed',
                'status' => $response->status(),
                'body' => $response->body()
            ];
        } catch (Exception $e) {
            Log::error('Tara Purchase Request Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Purchase verify
     * 
     * @param string $traceNumber Trace number from purchase request
     */
    public function purchaseVerify(string $traceNumber): array
    {
        if (!$this->token) {
            $loginResult = $this->login();
            if (!$loginResult['success']) {
                return $loginResult;
            }
        }

        try {
            $response = Http::withToken($this->token)
                ->post("{$this->baseUrl}/purchase/verify/{$traceNumber}", [
                    'traceNumber' => $traceNumber
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Purchase verify failed',
                'status' => $response->status(),
                'body' => $response->body()
            ];
        } catch (Exception $e) {
            Log::error('Tara Purchase Verify Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Purchase reverse/cancel
     * 
     * @param string $traceNumber Trace number from purchase request
     */
    public function purchaseReverse(string $traceNumber): array
    {
        if (!$this->token) {
            $loginResult = $this->login();
            if (!$loginResult['success']) {
                return $loginResult;
            }
        }

        try {
            $response = Http::withToken($this->token)
                ->post("{$this->baseUrl}/purchase/reverse/{$traceNumber}", [
                    'traceNumber' => $traceNumber
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Purchase reverse failed',
                'status' => $response->status(),
                'body' => $response->body()
            ];
        } catch (Exception $e) {
            Log::error('Tara Purchase Reverse Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Purchase inquiry
     * 
     * @param string $referenceOrTraceNumber Reference number or trace number
     */
    public function purchaseInquiry(string $referenceOrTraceNumber): array
    {
        if (!$this->token) {
            $loginResult = $this->login();
            if (!$loginResult['success']) {
                return $loginResult;
            }
        }

        try {
            $response = Http::withToken($this->token)
                ->post("{$this->baseUrl}/purchase/inquiry/{$referenceOrTraceNumber}", [
                    'id' => $referenceOrTraceNumber
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Purchase inquiry failed',
                'status' => $response->status(),
                'body' => $response->body()
            ];
        } catch (Exception $e) {
            Log::error('Tara Purchase Inquiry Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Helper method to create purchase trace payment data
     * 
     * @param int $barcode User ID (customer identifier)
     * @param int $amount Payment amount
     * @param int $data Additional data
     */
    public function createTracePayment(int $barcode, int $amount, int $data = 0): array
    {
        return [
            'barcode' => $barcode, // شناسه کاربری خریدار
            'amount' => $amount,
            'data' => $data
        ];
    }

    /**
     * Helper method to create purchase request item
     */
    public function createPurchaseItem(
        string $name,
        string $code,
        float $count,
        int $unit,
        int $fee,
        string $group,
        string $groupTitle,
        int $made,
        string $data = ""
    ): array {
        return [
            'name' => $name,
            'code' => $code,
            'count' => $count,
            'unit' => $unit, // 1.کیلوگرم 2.متر 3.لیتر 4.مترمربع 5.عدد 6.قطعه 7.دستگاه 8.دست 9.بسته 10.جعبه 11.ست 12.جفت
            'fee' => $fee,
            'group' => $group,
            'groupTitle' => $groupTitle,
            'made' => $made, // 0.نامشخص 1.ایرانی 2.خارجی
            'data' => $data
        ];
    }

    /**
     * Helper method to create purchase request invoice data
     */
    public function createInvoiceData(
        int $totalPrice,
        string $invoiceNumber,
        string $data,
        int $vat,
        array $items
    ): array {
        return [
            'totalPrice' => $totalPrice,
            'invoiceNumber' => $invoiceNumber,
            'data' => $data,
            'vat' => $vat,
            'items' => $items
        ];
    }

    /**
     * Helper method to create complete purchase request data
     */
    public function createPurchaseRequestData(
        int $amount,
        string $invoiceNumber,
        string $data,
        array $invoiceData
    ): array {
        return [
            'amount' => $amount,
            'invoiceNumber' => $invoiceNumber,
            'data' => $data,
            'invoiceData' => $invoiceData
        ];
    }

    /**
     * Get current token
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Set token manually
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * Get branch code
     */
    public function getBranchCode(): string
    {
        return $this->branchCode;
    }

    /**
     * Set branch code
     */
    public function setBranchCode(string $branchCode): void
    {
        $this->branchCode = $branchCode;
    }

    /**
     * Get terminal code from access code response
     * 
     * @param array $accessCodeResponse Response from getAccessCode()
     * @param int $terminalIndex Index of terminal to use (default: 0 for first terminal)
     * @return string|null Terminal code or null if not found
     */
    public function getTerminalCodeFromResponse(array $accessCodeResponse, int $terminalIndex = 0): ?string
    {
        if (!isset($accessCodeResponse['success']) || !$accessCodeResponse['success']) {
            return null;
        }

        $terminals = $accessCodeResponse['data'] ?? [];

        if (!isset($terminals[$terminalIndex])) {
            return null;
        }

        return $terminals[$terminalIndex]['terminalCode'] ?? null;
    }

    /**
     * Get all available terminals from access code response
     * 
     * @param array $accessCodeResponse Response from getAccessCode()
     * @return array Array of terminals with their info
     */
    public function getAvailableTerminals(array $accessCodeResponse): array
    {
        if (!isset($accessCodeResponse['success']) || !$accessCodeResponse['success']) {
            return [];
        }

        return $accessCodeResponse['data'] ?? [];
    }
}

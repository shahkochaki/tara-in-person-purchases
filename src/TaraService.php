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
    private ?int $tokenExpiresAt = null;
    private string $branchCode;
    private array $terminals = [];
    private ?string $selectedTerminalCode = null;
    private array $config;

    public function __construct(string $branchCode = null, array $config = null)
    {
        // Load configuration
        $this->config = $config ?? $this->loadConfig();

        $this->baseUrl = $this->config['base_url'] ?? 'https://stage.tara-club.ir/club/api/v1';
        $this->username = $this->config['credentials']['username'] ?? $this->config['username'] ?? '';
        $this->password = $this->config['credentials']['password'] ?? $this->config['password'] ?? '';
        $this->branchCode = $branchCode ?? $this->config['default_branch_code'] ?? $this->config['branch_code'] ?? '';

        // Validate required credentials
        if (empty($this->username) || empty($this->password) || empty($this->branchCode)) {
            throw new Exception('Required credentials (username, password, branch_code) are missing. Please set them in config or environment variables.');
        }
    }

    /**
     * Load configuration from file or return defaults
     */
    private function loadConfig(): array
    {
        $configPath = __DIR__ . '/../config/tara.php';

        if (file_exists($configPath)) {
            $config = include $configPath;
            return is_array($config) ? $config : [];
        }

        // Default configuration if file doesn't exist - NO SENSITIVE DEFAULTS
        return [
            'base_url' => 'https://stage.tara-club.ir/club/api/v1',
            'credentials' => [
                'username' => '',
                'password' => '',
            ],
            'default_branch_code' => '',
            'token' => [
                'buffer_seconds' => 60,
            ],
            'logging' => [
                'enabled' => true,
            ],
        ];
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

                // Calculate token expiry time (expiryDuration is in milliseconds)
                if (isset($data['expiryDuration'])) {
                    $this->tokenExpiresAt = time() + (int)($data['expiryDuration'] / 1000);
                }

                return [
                    'success' => true,
                    'data' => $data,
                    'token' => $this->token,
                    'expiresAt' => $this->tokenExpiresAt
                ];
            }

            return $this->parseErrorResponse($response);
        } catch (Exception $e) {
            Log::error('Tara Login Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check if current token is valid and not expired
     */
    private function isTokenValid(): bool
    {
        $bufferSeconds = $this->config['token']['buffer_seconds'] ?? 60;

        return $this->token &&
            $this->tokenExpiresAt &&
            time() < ($this->tokenExpiresAt - $bufferSeconds);
    }

    /**
     * Parse error response and extract error details
     */
    private function parseErrorResponse($response): array
    {
        $errorData = [
            'success' => false,
            'status' => $response->status(),
            'body' => $response->body()
        ];

        // Try to parse JSON response
        $jsonResponse = $response->json();
        if ($jsonResponse) {
            // Check for API error format: {"data":{"code":84780028,"message":"موجودی کافی نیست."},"success":false}
            if (isset($jsonResponse['data']) && is_array($jsonResponse['data'])) {
                $data = $jsonResponse['data'];

                if (isset($data['code']) && isset($data['message'])) {
                    $errorData['error'] = [
                        'title' => 'API Error',
                        'code' => $data['code'],
                        'message' => $data['message']
                    ];
                } else {
                    $errorData['error'] = [
                        'title' => 'API Error',
                        'code' => 0,
                        'message' => 'API request failed'
                    ];
                }

                // Include full data for debugging
                $errorData['error_data'] = $data;
            } else {
                $errorData['error'] = [
                    'title' => 'API Error',
                    'code' => 0,
                    'message' => $jsonResponse['message'] ?? 'API request failed'
                ];
            }

            // Include full response for debugging
            $errorData['full_response'] = $jsonResponse;
        } else {
            $errorData['error'] = [
                'title' => 'Connection Error',
                'code' => 0,
                'message' => 'API request failed - Invalid response format'
            ];
        }

        return $errorData;
    }

    /**
     * Get terminal access code for a specific terminal
     */
    private function getTerminalAccessCode(string $terminalCode): ?string
    {
        foreach ($this->terminals as $terminal) {
            if ($terminal['terminalCode'] === $terminalCode) {
                return $terminal['accessCode'] ?? null;
            }
        }
        return null;
    }

    /**
     * Get access code for branch
     */
    public function getAccessCode(string $branchCode = null): array
    {
        $branchCode = $branchCode ?? $this->branchCode;

        if (!$this->isTokenValid()) {
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
                $terminals = $response->json();

                // Store terminals for later use
                $this->terminals = $terminals;

                return [
                    'success' => true,
                    'data' => $terminals
                ];
            }

            return $this->parseErrorResponse($response);
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

            return $this->parseErrorResponse($response);
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
        // If no terminal code provided, use the selected one or first available
        if (!$terminalCode) {
            $terminalCode = $this->selectedTerminalCode;
            if (!$terminalCode && !empty($this->terminals)) {
                $terminalCode = $this->terminals[0]['terminalCode'] ?? null;
            }
        }

        if (!$terminalCode) {
            return [
                'success' => false,
                'error' => 'Terminal code not provided and no terminals available'
            ];
        }

        // Get the terminal access token for this terminal
        $terminalToken = $this->getTerminalAccessCode($terminalCode);
        if (!$terminalToken) {
            return [
                'success' => false,
                'error' => 'Terminal access code not found for terminal: ' . $terminalCode
            ];
        }

        try {
            $response = Http::withToken($terminalToken)
                ->post("{$this->baseUrl}/purchase/trace/{$terminalCode}", [
                    'terminalCode' => (int)$terminalCode,
                    'payment' => $payment
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return $this->parseErrorResponse($response);
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
     * @param string $terminalCode Terminal code (optional, uses selected terminal if not provided)
     */
    public function purchaseRequest(array $purchaseData, string $traceNumber, string $terminalCode = null): array
    {
        // Get terminal code
        if (!$terminalCode) {
            $terminalCode = $this->selectedTerminalCode;
            if (!$terminalCode && !empty($this->terminals)) {
                $terminalCode = $this->terminals[0]['terminalCode'] ?? null;
            }
        }

        if (!$terminalCode) {
            return [
                'success' => false,
                'error' => 'Terminal code not provided and no terminals available'
            ];
        }

        // Get the terminal access token for this terminal
        $terminalToken = $this->getTerminalAccessCode($terminalCode);
        if (!$terminalToken) {
            return [
                'success' => false,
                'error' => 'Terminal access code not found for terminal: ' . $terminalCode
            ];
        }

        try {
            // Add traceNumber to body
            $purchaseData['traceNumber'] = $traceNumber;

            $response = Http::withToken($terminalToken)
                ->post("{$this->baseUrl}/purchase/request/{$traceNumber}", $purchaseData);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return $this->parseErrorResponse($response);
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
     * @param string $terminalCode Terminal code (optional, uses selected terminal if not provided)
     */
    public function purchaseVerify(string $traceNumber, string $terminalCode = null): array
    {
        // Get terminal code
        if (!$terminalCode) {
            $terminalCode = $this->selectedTerminalCode;
            if (!$terminalCode && !empty($this->terminals)) {
                $terminalCode = $this->terminals[0]['terminalCode'] ?? null;
            }
        }

        if (!$terminalCode) {
            return [
                'success' => false,
                'error' => 'Terminal code not provided and no terminals available'
            ];
        }

        // Get the terminal access token for this terminal
        $terminalToken = $this->getTerminalAccessCode($terminalCode);
        if (!$terminalToken) {
            return [
                'success' => false,
                'error' => 'Terminal access code not found for terminal: ' . $terminalCode
            ];
        }

        try {
            $response = Http::withToken($terminalToken)
                ->post("{$this->baseUrl}/purchase/verify/{$traceNumber}", [
                    'traceNumber' => $traceNumber
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return $this->parseErrorResponse($response);
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
     * @param string $terminalCode Terminal code (optional, uses selected terminal if not provided)
     */
    public function purchaseReverse(string $traceNumber, string $terminalCode = null): array
    {
        // Get terminal code
        if (!$terminalCode) {
            $terminalCode = $this->selectedTerminalCode;
            if (!$terminalCode && !empty($this->terminals)) {
                $terminalCode = $this->terminals[0]['terminalCode'] ?? null;
            }
        }

        if (!$terminalCode) {
            return [
                'success' => false,
                'error' => 'Terminal code not provided and no terminals available'
            ];
        }

        // Get the terminal access token for this terminal
        $terminalToken = $this->getTerminalAccessCode($terminalCode);
        if (!$terminalToken) {
            return [
                'success' => false,
                'error' => 'Terminal access code not found for terminal: ' . $terminalCode
            ];
        }

        try {
            $response = Http::withToken($terminalToken)
                ->post("{$this->baseUrl}/purchase/reverse/{$traceNumber}", [
                    'traceNumber' => $traceNumber
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return $this->parseErrorResponse($response);
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
     * @param string $terminalCode Terminal code (optional, uses selected terminal if not provided)
     */
    public function purchaseInquiry(string $referenceOrTraceNumber, string $terminalCode = null): array
    {
        // Get terminal code
        if (!$terminalCode) {
            $terminalCode = $this->selectedTerminalCode;
            if (!$terminalCode && !empty($this->terminals)) {
                $terminalCode = $this->terminals[0]['terminalCode'] ?? null;
            }
        }

        if (!$terminalCode) {
            return [
                'success' => false,
                'error' => 'Terminal code not provided and no terminals available'
            ];
        }

        // Get the terminal access token for this terminal
        $terminalToken = $this->getTerminalAccessCode($terminalCode);
        if (!$terminalToken) {
            return [
                'success' => false,
                'error' => 'Terminal access code not found for terminal: ' . $terminalCode
            ];
        }

        try {
            $response = Http::withToken($terminalToken)
                ->post("{$this->baseUrl}/purchase/inquiry/{$referenceOrTraceNumber}", [
                    'id' => $referenceOrTraceNumber
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return $this->parseErrorResponse($response);
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
            'barcode' => str_replace(' ', '', $barcode), // Customer user ID
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
            'unit' => $unit, // 1:kg 2:meter 3:liter 4:sqm 5:piece 6:item 7:device 8:pair 9:pack 10:box 11:set 12:couple
            'fee' => $fee,
            'group' => $group,
            'groupTitle' => $groupTitle,
            'made' => $made, // 0:unknown 1:Iranian 2:foreign
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

    /**
     * Select a terminal by terminal code
     * 
     * @param string $terminalCode Terminal code to select
     * @return bool True if terminal was found and selected
     */
    public function selectTerminal(string $terminalCode): bool
    {
        foreach ($this->terminals as $terminal) {
            if ($terminal['terminalCode'] === $terminalCode) {
                $this->selectedTerminalCode = $terminalCode;
                return true;
            }
        }
        return false;
    }

    /**
     * Get currently selected terminal info
     * 
     * @return array|null Terminal info or null if none selected
     */
    public function getSelectedTerminal(): ?array
    {
        if (!$this->selectedTerminalCode) {
            return null;
        }

        foreach ($this->terminals as $terminal) {
            if ($terminal['terminalCode'] === $this->selectedTerminalCode) {
                return $terminal;
            }
        }
        return null;
    }

    /**
     * Get all stored terminals
     * 
     * @return array Array of terminals
     */
    public function getTerminals(): array
    {
        return $this->terminals;
    }

    /**
     * Get merchant code from selected terminal or first available terminal
     * 
     * @return string|null Merchant code or null if not available
     */
    public function getMerchantCode(): ?string
    {
        $terminal = $this->getSelectedTerminal();
        if (!$terminal && !empty($this->terminals)) {
            $terminal = $this->terminals[0];
        }

        return $terminal['merchantCode'] ?? null;
    }

    /**
     * Initialize complete session: login + get terminals
     * 
     * @param string|null $branchCode Branch code to use
     * @return array Result with success status and data
     */
    public function initializeSession(string $branchCode = null): array
    {
        // Step 1: Login
        $loginResult = $this->login();
        if (!$loginResult['success']) {
            return $loginResult;
        }

        // Step 2: Get terminals
        $accessResult = $this->getAccessCode($branchCode);
        if (!$accessResult['success']) {
            return $accessResult;
        }

        return [
            'success' => true,
            'login' => $loginResult,
            'terminals' => $accessResult,
            'availableTerminals' => $this->terminals
        ];
    }

    /**
     * Complete purchase flow: trace -> request -> verify
     * 
     * @param array $payment Payment data for trace
     * @param array $purchaseData Purchase request data
     * @param string|null $terminalCode Terminal code to use
     * @return array Complete flow result
     */
    public function completePurchaseFlow(array $payment, array $purchaseData, string $terminalCode = null): array
    {
        // Step 1: Purchase trace
        $traceResult = $this->purchaseTrace($payment, $terminalCode);
        if (!$traceResult['success']) {
            return [
                'success' => false,
                'error' => 'Purchase trace failed',
                'details' => $traceResult
            ];
        }

        $traceNumber = $traceResult['data']['traceNumber'] ?? null;
        if (!$traceNumber) {
            return [
                'success' => false,
                'error' => 'Trace number not found in response',
                'details' => $traceResult
            ];
        }

        // Step 2: Purchase request
        $requestResult = $this->purchaseRequest($purchaseData, $traceNumber, $terminalCode);
        if (!$requestResult['success']) {
            return [
                'success' => false,
                'error' => 'Purchase request failed',
                'trace' => $traceResult,
                'details' => $requestResult
            ];
        }

        // Step 3: Purchase verify
        $verifyResult = $this->purchaseVerify($traceNumber, $terminalCode);

        return [
            'success' => $verifyResult['success'],
            'trace' => $traceResult,
            'request' => $requestResult,
            'verify' => $verifyResult,
            'traceNumber' => $traceNumber
        ];
    }

    /**
     * Get configuration value
     * 
     * @param string $key Configuration key (dot notation supported)
     * @param mixed $default Default value if key not found
     * @return mixed Configuration value
     */
    public function getConfig(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->config;
        }

        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Set configuration value
     * 
     * @param string $key Configuration key (dot notation supported)
     * @param mixed $value Value to set
     * @return void
     */
    public function setConfig(string $key, $value): void
    {
        $keys = explode('.', $key);
        $config = &$this->config;

        foreach ($keys as $k) {
            if (!isset($config[$k]) || !is_array($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }

        $config = $value;
    }

    /**
     * Update credentials
     * 
     * @param string $username New username
     * @param string $password New password
     * @return void
     */
    public function updateCredentials(string $username, string $password): void
    {
        $this->username = $username;
        $this->password = $password;
        $this->setConfig('credentials.username', $username);
        $this->setConfig('credentials.password', $password);

        // Clear token to force re-login
        $this->token = null;
        $this->tokenExpiresAt = null;
    }

    /**
     * Update base URL
     * 
     * @param string $baseUrl New base URL
     * @return void
     */
    public function updateBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->setConfig('base_url', $this->baseUrl);
    }

    /**
     * Get current credentials
     * 
     * @return array Current username and password (password masked)
     */
    public function getCredentials(): array
    {
        return [
            'username' => $this->username,
            'password' => '***masked***'
        ];
    }

    /**
     * Get current base URL
     * 
     * @return string Current base URL
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Check if logging is enabled
     * 
     * @return bool Whether logging is enabled
     */
    public function isLoggingEnabled(): bool
    {
        return $this->getConfig('logging.enabled', true);
    }

    /**
     * Get timeout settings
     * 
     * @return array Timeout configuration
     */
    public function getTimeoutSettings(): array
    {
        return $this->getConfig('timeout', [
            'connect' => 30,
            'request' => 60
        ]);
    }
}

<?php

namespace Shahkochaki\TaraService;

use Exception;

/**
 * Example showing how to use TaraService with custom configuration
 */
class TaraConfigExample
{
    /**
     * Example 1: Using default configuration from config file
     */
    public function exampleDefaultConfig()
    {
        // Note: Make sure to set your credentials in .env file first
        try {
            $tara = new TaraService();

            echo "Base URL: " . $tara->getBaseUrl() . "\n";
            echo "Branch Code: " . $tara->getBranchCode() . "\n";
            echo "Credentials: " . json_encode($tara->getCredentials()) . "\n";

            // Initialize session
            $sessionResult = $tara->initializeSession();
            if ($sessionResult['success']) {
                echo "Session initialized successfully!\n";
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            echo "Please set TARA_USERNAME, TARA_PASSWORD, and TARA_BRANCH_CODE in your .env file\n";
        }
    }

    /**
     * Example 2: Using custom configuration array
     */
    public function exampleCustomConfig()
    {
        // Custom configuration - replace with your actual credentials
        $customConfig = [
            'base_url' => 'https://api.tara-club.ir/club/api/v1', // Production URL
            'credentials' => [
                'username' => 'your_production_user', // Replace with actual username
                'password' => 'your_production_pass'  // Replace with actual password
            ],
            'default_branch_code' => 'your_branch_code', // Replace with actual branch code
            'token' => [
                'buffer_seconds' => 120, // 2 minutes buffer
            ],
            'logging' => [
                'enabled' => false, // Disable logging
            ]
        ];

        try {
            $tara = new TaraService('your_branch_code', $customConfig);

            echo "Using custom config:\n";
            echo "Base URL: " . $tara->getBaseUrl() . "\n";
            echo "Branch Code: " . $tara->getBranchCode() . "\n";
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            echo "Please replace placeholder values with actual credentials\n";
        }
    }

    /**
     * Example 3: Updating configuration at runtime
     */
    public function exampleRuntimeConfig()
    {
        try {
            // Start with environment configuration
            $tara = new TaraService();

            // Update credentials (replace with actual values)
            $tara->updateCredentials('your_new_username', 'your_new_password');
            echo "Updated credentials: " . json_encode($tara->getCredentials()) . "\n";

            // Update base URL
            $tara->updateBaseUrl('https://new-api.tara-club.ir/club/api/v1');
            echo "Updated base URL: " . $tara->getBaseUrl() . "\n";

            // Update individual config values
            $tara->setConfig('token.buffer_seconds', 180);
            $tara->setConfig('logging.enabled', false);

            echo "Token buffer: " . $tara->getConfig('token.buffer_seconds') . " seconds\n";
            echo "Logging enabled: " . ($tara->isLoggingEnabled() ? 'Yes' : 'No') . "\n";
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            echo "Please ensure credentials are set in environment variables\n";
        }
    }

    /**
     * Example 4: Environment-based configuration
     */
    public function exampleEnvironmentConfig()
    {
        // Simulate different environments
        $environments = [
            'development' => [
                'base_url' => 'https://dev.tara-club.ir/club/api/v1',
                'credentials' => [
                    'username' => 'dev_user',
                    'password' => 'dev_pass'
                ],
                'logging' => ['enabled' => true, 'level' => 'debug']
            ],
            'staging' => [
                'base_url' => 'https://stage.tara-club.ir/club/api/v1',
                'credentials' => [
                    'username' => 'stage_user',
                    'password' => 'stage_pass'
                ],
                'logging' => ['enabled' => true, 'level' => 'info']
            ],
            'production' => [
                'base_url' => 'https://api.tara-club.ir/club/api/v1',
                'credentials' => [
                    'username' => 'prod_user',
                    'password' => 'prod_pass'
                ],
                'logging' => ['enabled' => false]
            ]
        ];

        $environment = 'staging'; // Could be from $_ENV or other source

        if (isset($environments[$environment])) {
            $config = $environments[$environment];
            $tara = new TaraService('BRANCH_CODE', $config);

            echo "Environment: {$environment}\n";
            echo "Base URL: " . $tara->getBaseUrl() . "\n";
            echo "Logging: " . ($tara->isLoggingEnabled() ? 'Enabled' : 'Disabled') . "\n";
        }
    }

    /**
     * Example 5: Using configuration with helper functions
     */
    public function exampleConfigHelpers()
    {
        $tara = new TaraService();

        // Get various configuration values
        echo "Full config: " . json_encode($tara->getConfig(), JSON_PRETTY_PRINT) . "\n";

        echo "Timeout settings: " . json_encode($tara->getTimeoutSettings()) . "\n";

        echo "Token buffer: " . $tara->getConfig('token.buffer_seconds', 60) . " seconds\n";

        echo "Log level: " . $tara->getConfig('logging.level', 'info') . "\n";

        // Check if specific features are enabled
        if ($tara->getConfig('retry.enabled', false)) {
            echo "Retry is enabled with " . $tara->getConfig('retry.max_attempts', 3) . " max attempts\n";
        }
    }

    /**
     * Example 6: Configuration validation
     */
    public function exampleConfigValidation()
    {
        $tara = new TaraService();

        // Validate required configuration
        $requiredConfig = ['base_url', 'credentials.username', 'credentials.password'];

        foreach ($requiredConfig as $key) {
            $value = $tara->getConfig($key);
            if (empty($value)) {
                echo "Warning: Required configuration '{$key}' is missing or empty\n";
            } else {
                echo "✓ Configuration '{$key}' is set\n";
            }
        }

        // Validate base URL format
        $baseUrl = $tara->getBaseUrl();
        if (!filter_var($baseUrl, FILTER_VALIDATE_URL)) {
            echo "Warning: Base URL '{$baseUrl}' is not a valid URL\n";
        } else {
            echo "✓ Base URL is valid\n";
        }
    }

    /**
     * Run all examples
     */
    public function runAllExamples()
    {
        echo "=== Example 1: Default Config ===\n";
        $this->exampleDefaultConfig();

        echo "\n=== Example 2: Custom Config ===\n";
        $this->exampleCustomConfig();

        echo "\n=== Example 3: Runtime Config ===\n";
        $this->exampleRuntimeConfig();

        echo "\n=== Example 4: Environment Config ===\n";
        $this->exampleEnvironmentConfig();

        echo "\n=== Example 5: Config Helpers ===\n";
        $this->exampleConfigHelpers();

        echo "\n=== Example 6: Config Validation ===\n";
        $this->exampleConfigValidation();
    }
}

<?php

namespace Shahkochaki\TaraService;

use Exception;

/**
 * Tara API Exception
 */
class TaraException extends Exception
{
    private ?array $responseData = null;
    private ?int $httpStatusCode = null;

    public function __construct(
        string $message = "",
        int $code = 0,
        ?Exception $previous = null,
        ?array $responseData = null,
        ?int $httpStatusCode = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->responseData = $responseData;
        $this->httpStatusCode = $httpStatusCode;
    }

    /**
     * Get API response data
     */
    public function getResponseData(): ?array
    {
        return $this->responseData;
    }

    /**
     * Get HTTP status code
     */
    public function getHttpStatusCode(): ?int
    {
        return $this->httpStatusCode;
    }

    /**
     * Convert to array for display
     */
    public function toArray(): array
    {
        return [
            'success' => false,
            'error' => $this->getMessage(),
            'code' => $this->getCode(),
            'http_status' => $this->httpStatusCode,
            'response_data' => $this->responseData
        ];
    }
}

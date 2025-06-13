<?php

namespace Paymongo;

// Import all necessary classes for type hinting and instantiation.
use Paymongo\Exceptions\ApiException;
use Paymongo\Exceptions\AuthenticationException;
use Paymongo\Exceptions\BaseException;
use Paymongo\Exceptions\InvalidRequestException;
use Paymongo\Exceptions\ResourceNotFoundException;
use Paymongo\Exceptions\RouteNotFoundException;

class HttpClient
{
    const DEFAULT_CONNECTTIMEOUT = 30;
    const DEFAULT_TIMEOUT = 30;

    private string $apiKey;

    public function __construct(string $apiKey = '')
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param array $opts The options for the request.
     * @return ApiResource The parsed API resource from the response.
     * @throws ApiException
     */
    public function request(array $opts): ApiResource
    {
        // Set variables with defaults for clarity and safety.
        $method = $opts['method'] ?? 'GET';
        $url = $opts['url'] ?? '';
        $params = $opts['params'] ?? [];

        if ($method === 'GET' && !empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $ch = curl_init($url);

        $headers = [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($this->apiKey . ':'),
        ];

        curl_setopt_array($ch, [
            CURLOPT_CONNECTTIMEOUT => self::DEFAULT_CONNECTTIMEOUT,
            CURLOPT_TIMEOUT        => self::DEFAULT_TIMEOUT,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
        ]);

        if (in_array($method, ['POST', 'PUT', 'DELETE'], true)) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

            if (!empty($params)) {
                $payload = json_encode(['data' => ['attributes' => $params]]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            }
        }

        $body = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($body === false) {
            // Handle cURL-level errors, like a timeout or connection failure.
            $curlError = curl_error($ch);
            curl_close($ch);
            throw new ApiException("cURL Error: {$curlError}"); // Throws a generic API exception for transport errors.
        }

        if ($httpCode >= 400) {
            // Let the dedicated handler throw the correct API exception.
            $this->handleErrorResponse($body, $httpCode);
        }

        curl_close($ch);

        $decodedBody = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException('Invalid JSON response from the API.', [], $httpCode);
        }

        return new ApiResource($decodedBody);
    }

    /**
     * Throws the appropriate exception based on the HTTP status code.
     *
     * @param string $body The raw response body.
     * @param int $httpCode The HTTP status code.
     * @throws BaseException
     */
    private function handleErrorResponse(string $body, int $httpCode): void
    {
        $decodedBody = json_decode($body, true) ?? [];
        $message = "PayMongo API Error - HTTP {$httpCode}";

        // This is the updated logic using our new exception hierarchy
        switch ($httpCode) {
            case 400:
                throw new InvalidRequestException($message, $decodedBody, $httpCode);
            case 401:
                throw new AuthenticationException($message, $decodedBody, $httpCode);
            case 404:
                // Special handling for 404s to differentiate between a bad URL and a missing resource
                if (empty($body)) {
                    throw new RouteNotFoundException('The requested API route does not exist.');
                }
                throw new ResourceNotFoundException($message, $decodedBody, $httpCode);
            default:
                // For all other errors (e.g., 500), throw the general ApiException.
                throw new ApiException($message, $decodedBody, $httpCode);
        }
    }
}

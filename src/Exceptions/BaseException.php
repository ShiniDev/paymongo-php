<?php

namespace Paymongo\Exceptions;

use Exception;
use Paymongo\Entities\Error;
use Throwable;

class BaseException extends Exception
{
    protected array $errorData;

    /**
     * @var Error[]|null Caches the instantiated Error objects.
     */
    protected ?array $errors = null;

    /**
     * @param string $message The exception message.
     * @param array $data The decoded JSON data from the API error response.
     * @param int $code The exception code.
     * @param Throwable|null $previous The previous throwable for exception chaining.
     */
    public function __construct(string $message = "", array $data = [], int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorData = $data['errors'] ?? [];
    }

    /**
     * Returns an array of Error objects from the API response.
     *
     * @return Error[]
     */
    public function getErrors(): array
    {
        if ($this->errors === null) {
            $this->errors = array_map(
                fn(array $error) => new Error($error),
                $this->errorData
            );
        }

        return $this->errors;
    }
}

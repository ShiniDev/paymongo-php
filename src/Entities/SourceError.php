<?php

namespace Paymongo\Entities;

/**
 * Represents the source of a specific API error.
 */
class SourceError
{
    /**
     * A JSON Pointer to the field in the request body that caused the error.
     * @var string|null
     */
    public ?string $pointer;

    /**
     * The name of the attribute that caused the error.
     * @var string|null
     */
    public ?string $attribute;

    /**
     * @param array $source The source data array from an error object.
     */
    public function __construct(array $source)
    {
        $this->pointer = $source['pointer'] ?? null;
        $this->attribute = $source['attribute'] ?? null;
    }
}

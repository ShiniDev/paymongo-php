<?php

namespace Paymongo\Entities;

/**
 * Represents a single error object from a PayMongo API response.
 */
class Error
{
    public ?string $code;
    public ?string $detail;

    /**
     * The source of the error, if applicable.
     * @var SourceError|null
     */
    public ?SourceError $source;

    public function __construct(array $data)
    {
        $this->code = $data['code'] ?? null;
        $this->detail = $data['detail'] ?? null; // Safely assigns detail

        // Directly checks for the source data and creates a SourceError object if it exists.
        $sourceData = $data['source'] ?? null;
        $this->source = is_array($sourceData) ? new SourceError($sourceData) : null;
    }

    /**
     * A public helper method to check if the error object contains a source.
     */
    public function hasSource(): bool
    {
        return $this->source !== null;
    }
}

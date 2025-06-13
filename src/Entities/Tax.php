<?php

namespace Paymongo\Entities;

/**
 * Represents a single Tax object associated with a Link.
 */
class Tax
{
    /** @var int|null */
    public ?int $amount;

    /** @var string|null */
    public ?string $currency;

    /** @var bool|null */
    public ?bool $inclusive;

    /** @var string|null */
    public ?string $name;

    /** @var string|null */
    public ?string $type;

    /** @var string|null */
    public ?string $value;

    /**
     * @param array $data The tax data, passed as an array.
     */
    public function __construct(array $data)
    {
        $this->amount = $data['amount'] ?? null;
        $this->currency = $data['currency'] ?? null;
        $this->inclusive = $data['inclusive'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->value = $data['value'] ?? null;
    }
}

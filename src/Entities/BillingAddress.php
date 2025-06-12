<?php

namespace Paymongo\Entities;

class BillingAddress
{
    /** @var string|null */
    public ?string $city;

    /** @var string|null */
    public ?string $country;

    /** @var string|null */
    public ?string $line1;

    /** @var string|null */
    public ?string $line2;

    /** @var string|null */
    public ?string $postal_code;

    /** @var string|null */
    public ?string $state;

    /**
     * @param array $data The address data from the API.
     */
    public function __construct(array $data)
    {
        $this->city = $data['city'] ?? null;
        $this->country = $data['country'] ?? null;
        $this->line1 = $data['line1'] ?? null;
        $this->line2 = $data['line2'] ?? null;
        $this->postal_code = $data['postal_code'] ?? null;
        $this->state = $data['state'] ?? null;
    }
}

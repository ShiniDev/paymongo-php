<?php

namespace Paymongo\Entities;

// Import the BillingAddress class for cleaner code.
use Paymongo\Entities\BillingAddress;

class Billing
{
    /**
     * @var BillingAddress|null
     */
    public ?BillingAddress $address;

    /**
     * @var string|null
     */
    public ?string $name;

    /**
     * @var string|null
     */
    public ?string $email;

    /**
     * @var string|null
     */
    public ?string $phone;

    public function __construct(array $data)
    {
        $this->address = isset($data['address']) ? new BillingAddress($data['address']) : null;
        $this->name = $data['name'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->phone = $data['phone'] ?? null;
    }
}

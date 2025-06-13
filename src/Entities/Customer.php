<?php

namespace Paymongo\Entities;

class Customer extends \Paymongo\Entities\BaseEntity
{
    public ?string $id;
    public ?string $default_device;
    public ?string $default_payment_method_id;
    public ?string $email;
    public ?string $first_name;
    public ?string $last_name;
    public ?bool $livemode;
    public ?string $organization_id;
    public ?string $phone;
    public ?int $created_at;
    public ?int $updated_at;

    /**
     * @param object $apiResource The raw customer object from the API.
     */
    public function __construct(object $apiResource)
    {
        $this->id = $apiResource->id ?? null;

        $attributes = (array) $apiResource->attributes ?? [];

        $this->default_device = $attributes['default_device'] ?? null;
        $this->default_payment_method_id = $attributes['default_payment_method_id'] ?? null;
        $this->email = $attributes['email'] ?? null;
        $this->first_name = $attributes['first_name'] ?? null;
        $this->last_name = $attributes['last_name'] ?? null;
        $this->livemode = $attributes['livemode'] ?? null;
        $this->organization_id = $attributes['organization_id'] ?? null;
        $this->phone = $attributes['phone'] ?? null;
        $this->created_at = $attributes['created_at'] ?? null;
        $this->updated_at = $attributes['updated_at'] ?? null;
    }
}

<?php

namespace Paymongo\Entities;

/**
 * Represents the 'redirect' object, containing URLs for redirection flows.
 */
class Redirect
{
    /** @var string|null */
    public ?string $checkout_url;

    /** @var string|null */
    public ?string $success_url;

    /** @var string|null */
    public ?string $failed_url;

    /**
     * @param array $data The redirect data, passed as an array.
     */
    public function __construct(array $data)
    {
        $this->checkout_url = $data['checkout_url'] ?? null;
        $this->success_url = $data['success_url'] ?? null;
        $this->failed_url = $data['failed_url'] ?? null;
    }
}

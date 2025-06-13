<?php

namespace Paymongo\Entities;

/**
 * Represents the 'redirect_to_url' object within a NextAction.
 */
class RedirectToUrl
{
    /** The URL to redirect the user to for authentication. @var string|null */
    public ?string $url;

    /** The URL to return to after authentication. @var string|null */
    public ?string $return_url;

    public function __construct(array $data)
    {
        $this->url = $data['url'] ?? null;
        $this->return_url = $data['return_url'] ?? null;
    }
}

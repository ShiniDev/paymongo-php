<?php

namespace Paymongo\Entities;

class NextAction
{
    /** The type of next action required. @var string|null */
    public ?string $type;

    /** The redirect details, if applicable. @var \Paymongo\Entities\RedirectToUrl|null */
    public ?RedirectToUrl $redirect_to_url; // Property name updated

    public function __construct(array $data)
    {
        $this->type = $data['type'] ?? null;

        // Key name updated to match official documentation
        $redirectData = $data['redirect_to_url'] ?? null;
        $this->redirect_to_url = is_null($redirectData) ? null : new RedirectToUrl($redirectData);
    }
}

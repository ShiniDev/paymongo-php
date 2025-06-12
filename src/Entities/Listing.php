<?php

namespace Paymongo\Entities;

class Listing
{
    /**
     * True if there are more resources available.
     * @var bool
     */
    public bool $has_more;

    /**
     * The list of resource objects for the current page.
     * @var array
     */
    public array $data;

    /**
     * @param array $data The raw listing object from the API.
     */
    public function __construct(array $data)
    {
        $hasMoreValue = $data['has_more'] ?? false;

        // This logic precisely matches the original, safely checking for `true` or `1`.
        $this->has_more = ($hasMoreValue === true || $hasMoreValue === 1);

        $this->data = $data['data'] ?? [];
    }
}

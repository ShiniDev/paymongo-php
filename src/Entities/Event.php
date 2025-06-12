<?php

namespace Paymongo\Entities;

// We extend the empty BaseEntity for structural consistency.
class Event extends \Paymongo\Entities\BaseEntity
{
    /**
     * The unique identifier of the Event (e.g., 'evt_...').
     * @var string|null
     */
    public ?string $id;

    /**
     * The type of event (e.g., 'source.chargeable').
     * @var string|null
     */
    public ?string $type;

    /**
     * The API resource that the event is about. Can be a Payment, Source, etc.
     * @var object|array|null
     */
    public $resource; // PHP 7.4 has no 'mixed' property type, so this is left untyped.

    /**
     * Constructs an Event object from a raw API response object.
     *
     * @param object $apiResource The event object from the PayMongo API.
     */
    public function __construct(object $apiResource)
    {
        // Safely access properties from the main event object.
        $this->id = $apiResource->id ?? null;

        $attributes = $apiResource->attributes ?? [];

        // Safely access properties from the nested 'attributes' object.
        $this->type = $attributes['type'] ?? null;
        $this->resource = $attributes['data'] ?? null;
    }
}

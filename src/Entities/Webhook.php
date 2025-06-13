<?php

namespace Paymongo\Entities;

class Webhook extends \Paymongo\Entities\BaseEntity
{
    public ?string $id;
    public ?bool $livemode;
    public ?string $secret_key;
    public ?string $url;
    public ?string $status;
    public ?int $created_at;
    public ?int $updated_at;

    /** @var string[]|null - The list of events this webhook listens for. */
    public ?array $events;

    /** @var object|array|null */
    public $metadata;

    /**
     * @param object $apiResource The raw webhook object from the API.
     */
    public function __construct(object $apiResource)
    {
        $this->id = $apiResource->id ?? null;

        $attributes = (array) $apiResource->attributes ?? [];

        $this->livemode = $attributes['livemode'] ?? null;
        $this->secret_key = $attributes['secret_key'] ?? null;
        $this->events = $attributes['events'] ?? null;
        $this->url = $attributes['url'] ?? null;
        $this->status = $attributes['status'] ?? null;
        $this->metadata = $attributes['metadata'] ?? null;
        $this->created_at = $attributes['created_at'] ?? null;
        $this->updated_at = $attributes['updated_at'] ?? null;
    }
}

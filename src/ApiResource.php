<?php

namespace Paymongo;

class ApiResource
{
    /**
     * The main data payload. For a single resource, this is the resource object.
     * For a list, this is the array of resource objects.
     * @var mixed
     */
    public $data;

    /**
     * The 'attributes' object of a single resource.
     * Set to null for list responses.
     * @var object|null
     */
    public ?object $attributes = null;

    /**
     * The 'id' of a single resource.
     * Set to null for list responses.
     * @var string|null
     */
    public ?string $id = null;

    /**
     * The 'has_more' flag from a list response.
     * Set to null for single resource responses.
     * @var bool|null
     */
    public ?bool $hasMore = null;

    /**
     * @param array $response The decoded JSON array from the HttpClient.
     */
    public function __construct(array $response)
    {
        // This is the core logic: if a 'data' key exists at the top level,
        // use its value. Otherwise, use the entire response.
        // This normalizes single vs. list responses.
        $this->data = $response['data'] ?? $response;

        // This handles list responses by extracting the 'has_more' flag.
        $this->hasMore = isset($response['has_more']) ? (bool) $response['has_more'] : null;

        // This handles single resource responses by "hoisting" the id and attributes
        // to the top level for easier access, which is why the code in the `all()`
        // methods (new Entity(new ApiResource($row))) works.
        if (is_array($this->data) && isset($this->data['attributes'])) {
            $this->attributes = (object) ($this->data['attributes'] ?? []);
            $this->id = $this->data['id'] ?? null;
        }
    }
}

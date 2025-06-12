<?php

namespace Paymongo\Entities;

class Refund extends \Paymongo\Entities\BaseEntity
{
    public ?string $id;
    public ?int $amount;
    public ?string $balance_transaction_id;
    public ?bool $livemode;
    public ?string $payment_id;
    public ?string $payout_id;
    public ?string $notes;
    public ?string $reason;
    public ?string $status;
    public ?int $available_at;
    public ?int $refunded_at;
    public ?string $currency;
    public ?int $created_at;
    public ?int $updated_at;

    /** @var object|array|null */
    public $metadata;

    /**
     * @param object $apiResource The raw refund object from the API.
     */
    public function __construct(object $apiResource)
    {
        $this->id = $apiResource->id ?? null;

        $attributes = $apiResource->attributes ?? [];

        $this->amount = $attributes['amount'] ?? null;
        $this->balance_transaction_id = $attributes['balance_transaction_id'] ?? null;
        $this->livemode = $attributes['livemode'] ?? null;
        $this->payment_id = $attributes['payment_id'] ?? null;
        $this->payout_id = $attributes['payout_id'] ?? null;
        $this->notes = $attributes['notes'] ?? null;
        $this->reason = $attributes['reason'] ?? null;
        $this->status = $attributes['status'] ?? null;
        $this->available_at = $attributes['available_at'] ?? null;
        $this->refunded_at = $attributes['refunded_at'] ?? null;
        $this->currency = $attributes['currency'] ?? null;
        $this->metadata = $attributes['metadata'] ?? null;
        $this->created_at = $attributes['created_at'] ?? null;
        $this->updated_at = $attributes['updated_at'] ?? null;
    }
}

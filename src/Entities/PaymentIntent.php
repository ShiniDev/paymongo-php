<?php

namespace Paymongo\Entities;

use Paymongo\Entities\Payment;

class PaymentIntent extends \Paymongo\Entities\BaseEntity
{
    public ?string $id;
    public ?int $amount;
    public ?string $capture_type;
    public ?string $client_key;
    public ?string $currency;
    public ?string $description;
    public ?bool $livemode;
    public ?string $statement_descriptor;
    public ?string $status;
    public ?string $setup_future_usage;
    public ?int $created_at;
    public ?int $updated_at;

    /** @var object|null - Describes the last payment error. */
    public ?LastPaymentError $last_payment_error;

    /** @var string[]|null - Array of allowed payment method types. */
    public ?array $payment_method_allowed;

    /** @var Payment[]|null - Array of payments made with this PaymentIntent. */
    public ?array $payments;

    /** @var object|null - The next action required to complete the payment. */
    public ?NextAction $next_action;

    /** @var object|null - Options for the payment method. */
    public ?PaymentMethodOptions $payment_method_options;

    /** @var object|array|null */
    public $metadata;

    /**
     * @param object $apiResource The raw PaymentIntent object from the API.
     */
    public function __construct(object $apiResource)
    {
        $this->id = $apiResource->id ?? null;

        $attributes = (array) $apiResource->attributes ?? [];

        $this->amount = $attributes['amount'] ?? null;
        $this->capture_type = $attributes['capture_type'] ?? null;
        $this->client_key = $attributes['client_key'] ?? null;
        $this->currency = $attributes['currency'] ?? null;
        $this->description = $attributes['description'] ?? null;
        $this->livemode = $attributes['livemode'] ?? null;
        $this->statement_descriptor = $attributes['statement_descriptor'] ?? null;
        $this->status = $attributes['status'] ?? null;
        $this->payment_method_allowed = $attributes['payment_method_allowed'] ?? null;

        $this->metadata = $attributes['metadata'] ?? null;
        $this->setup_future_usage = $attributes['setup_future_usage'] ?? null;
        $this->created_at = $attributes['created_at'] ?? null;
        $this->updated_at = $attributes['updated_at'] ?? null;

        $this->payments = null;
        if (!empty($attributes['payments']) && is_array($attributes['payments'])) {
            $this->payments = [];
            foreach ($attributes['payments'] as $paymentData) {
                $paymentData = (object) $paymentData;
                $this->payments[] = new Payment($paymentData);
            }
        }

        $this->last_payment_error = null;
        $last_payment_error = $attributes['last_payment_error'] ?? null;
        $last_payment_error = is_null($last_payment_error) ? null : new LastPaymentError($last_payment_error);
        $this->next_action = null;
        $next_action = $attributes['next_action'] ?? null;
        $next_action = is_null($next_action) ? null : new NextAction($next_action);
        $this->payment_method_options = null;
        $payment_method_options = $attributes['payment_method_options'] ?? null;
        $payment_method_options = is_null($payment_method_options) ? null : new PaymentMethodOptions($payment_method_options);
    }
}

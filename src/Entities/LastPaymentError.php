<?php

namespace Paymongo\Entities;

class LastPaymentError
{
    /** The failed PaymentMethod used. @var object|null */
    public ?object $payment_method;

    /** The reason for the failure. @var string|null */
    public ?string $reason;

    /** A human-readable message about the failure. @var string|null */
    public ?string $message;

    public function __construct(array $data)
    {
        // Property name updated to match official documentation
        $this->payment_method = isset($data['payment_method']) ? (object) $data['payment_method'] : null;
        $this->reason = $data['reason'] ?? null;
        $this->message = $data['message'] ?? null;
    }
}

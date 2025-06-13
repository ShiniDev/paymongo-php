<?php

namespace Paymongo\Entities;

class PaymentMethodOptions
{
    /** Options specific to card payment methods. @var \Paymongo\Entities\CardOptions|null */
    public ?CardOptions $card;

    public function __construct(array $data)
    {
        $cardData = $data['card'] ?? null;
        $this->card = is_null($cardData) ? null : new CardOptions($cardData);
    }
}

<?php

namespace Paymongo\Entities;

class CardOptions
{
    /** Instruction on when to perform 3D Secure authentication. @var string|null */
    public ?string $request_three_d_secure;

    public function __construct(array $data)
    {
        $this->request_three_d_secure = $data['request_three_d_secure'] ?? null;
    }
}

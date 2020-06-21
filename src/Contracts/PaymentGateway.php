<?php

namespace Madnest\MadstoreGopay\Contracts;

interface PaymentGateway
{
    public function createPayment(Purchasable $purchasable, array $params = [], array $options = []);
}

<?php

namespace Madnest\MadstoreGopay\Contracts;

interface ShippingItem
{
    public function getTitle(): string;

    public function getAmount(): int;
}

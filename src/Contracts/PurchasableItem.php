<?php

namespace Madnest\MadstoreGopay\Contracts;

interface PurchasableItem
{
    public function getTitle(): string;

    public function getUrl(): string;

    public function getEan(): string;

    public function getAmount(): int;

    public function getQuantity(): int;
}

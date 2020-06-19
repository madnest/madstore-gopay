<?php

namespace Madnest\MadstoreGopay\Contracts;

use Illuminate\Support\Collection;

interface Purchasable
{
    public function getLanguage();

    public function getCurrency();

    public function getUUID();

    public function getVarSymbol();

    public function getAmount(): int;

    public function getDiscountAmount(): int;

    public function getFinalAmount(): int;

    public function getItems(): Collection;

    public function getPayerData(): HasPayerData;
}

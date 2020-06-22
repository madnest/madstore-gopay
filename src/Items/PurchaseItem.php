<?php

namespace Madnest\MadstoreGopay\Items;

class PurchaseItem extends Item
{
    public function __construct(string $name, int $amount, int $quantity, int $vatRate)
    {
        $this->setType(\GoPay\Definition\Payment\PaymentItemType::ITEM);

        $this->setName($name);

        $this->setAmount($amount);

        $this->setQuantity($quantity);

        $this->setVATRate($vatRate);
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setVATRate(int $vatRate): void
    {
        $this->vat_rate = $vatRate;
    }

    public function getVATRate(): int
    {
        return $this->vat_rate;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setEan(string $ean): void
    {
        $this->ean = $ean;
    }

    public function getEan(): string
    {
        return $this->ean;
    }
}

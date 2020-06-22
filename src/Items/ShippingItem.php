<?php

namespace Madnest\MadstoreGopay\Items;

class ShippingItem extends Item
{
    public function __construct(string $name, int $amount, int $quantity = 1, int $vatRate = 21)
    {
        $this->setType(\GoPay\Definition\Payment\PaymentItemType::DELIVERY);

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
        $this->vatRate = $vatRate;
    }

    public function getVATRate(): int
    {
        return $this->vatRate;
    }
}

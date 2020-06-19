<?php

namespace Madnest\MadstoreGopay\Contracts;

interface HasPayerData
{
    public function getFirstName(): string;

    public function getLastName(): string;

    public function getEmail(): string;

    public function getPhoneNumber(): string;

    public function getCity(): string;

    public function getStreet(): string;

    public function getZipCode(): string;

    public function getCountryIso3Code(): string;
}

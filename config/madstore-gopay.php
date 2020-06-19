<?php

return [
    'default_payment_instrument' => \GoPay\Definition\Payment\PaymentInstrument::PAYMENT_CARD,

    'allowed_payment_instruments' => [
        \GoPay\Definition\Payment\PaymentInstrument::PAYMENT_CARD,
        \GoPay\Definition\Payment\PaymentInstrument::BANK_ACCOUNT,
        \GoPay\Definition\Payment\PaymentInstrument::PREMIUM_SMS,
        \GoPay\Definition\Payment\PaymentInstrument::MPAYMENT,
        \GoPay\Definition\Payment\PaymentInstrument::PAYSAFECARD,
        \GoPay\Definition\Payment\PaymentInstrument::SUPERCASH,
        \GoPay\Definition\Payment\PaymentInstrument::GOPAY,
        \GoPay\Definition\Payment\PaymentInstrument::PAYPAL,
        \GoPay\Definition\Payment\PaymentInstrument::BITCOIN,
        \GoPay\Definition\Payment\PaymentInstrument::ACCOUNT,
        \GoPay\Definition\Payment\PaymentInstrument::GPAY,
    ],

    'lang' => [
        'cs' => \GoPay\Definition\Language::CZECH,
        'sk' => \GoPay\Definition\Language::SLOVAK,
        'en' => \GoPay\Definition\Language::ENGLISH,
        'de' => \GoPay\Definition\Language::GERMAN,
    ],
];

<?php

return [
    'default_payment_instrument' => \GoPay\Definition\Payment\PaymentInstrument::PAYMENT_CARD,

    'eet' => false,

    'inline' => false,

    'return_url' => env('GOPAY_RETURN_URL'),
    'notification_url' => env('GOPAY_NOTIFY_URL'),

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

    'vat' => [
        '0' => \GoPay\Definition\Payment\VatRate::RATE_1,
        '10' => \GoPay\Definition\Payment\VatRate::RATE_2,
        '15' => \GoPay\Definition\Payment\VatRate::RATE_3,
        '21' => \GoPay\Definition\Payment\VatRate::RATE_4,
    ],
];

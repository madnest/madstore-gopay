<?php

namespace Madnest\MadstoreGopay;

use GoPay\Http\Response;
use Madnest\LaravelGopay\LaravelGopay;
use Madnest\MadstoreGopay\Contracts\HasOrderContactData;
use Madnest\MadstoreGopay\Contracts\HasPayerData;
use Madnest\MadstoreGopay\Contracts\Purchasable;
use Madnest\MadstoreGopay\Contracts\PurchasableItem;
use Madnest\MadstoreGopay\Contracts\ShippingItem;

class MadstoreGopay
{
    protected $gopay;

    public function __construct()
    {
        $this->gopay = new LaravelGopay;
    }

    /**
     * Creates standard GoPay payment
     *
     * @param Purchasable $purchasable
     * @param <array|array> $params Additional params
     * @param <array|array> $options Additional options
     * @return \GoPay\Http\Response
     */
    public function createPayment(Purchasable $purchasable, array $params = [], array $options = []): Response
    {
        return $this->gopay->createPayment($this->getParams($purchasable, $params, $options));
    }

    protected function getParams(Purchasable $order, array $params = [], array $options = []): array
    {
        return array_merge([
            'payer' => $this->getPayerData($order->getPayerData()),
            'amount' => $order->getFinalAmount(),
            'currency' => $order->getCurrency()->getCode(),
            'order_number' => $order->getVarSymbol(),
            'order_description' => $order->getUUID(),
            'items' => $this->getItems($order),
            'eet' => $this->getEET($order),
            'additional_params' => $params,
            'lang' => config("madstore-gopay.{$order->getLanguage()}"),
        ], $options);

        // Options example
        // $options = [
        //     'callback' => [
        //         'return_url' => 'https://www.eshop.cz/return',
        //         'notification_url' => 'https://www.eshop.cz/notify'
        //     ],
        // ];
    }

    /**
     * Loop through purchasable items and map them,
     * possible to also add discount items
     * and delivery item
     *
     * @param Purchasable $order
     * @return array
     */
    protected function getItems(Purchasable $order): array
    {
        return array_merge(
            $order->getItems()->map(fn ($item) => $this->getItem($item))->toArray(),
            // $this->getDeliveryItem($order->getShipping()),
        );
    }

    /**
     * Data mapping of one item
     *
     * @param PurchasableItem $item
     * @return array
     */
    protected function getItem(PurchasableItem $item): array
    {
        return [
            'type' => \GoPay\Definition\Payment\PaymentItemType::ITEM,
            'name' => $item->getTitle(),
            'product_url' => $item->getUrl(),
            'ean' => $item->getEan(),
            'amount' => $item->getAmount(),
            'count' => $item->getQuantity(),
            'vat_rate' => config(
                'madstore-gopay.vat.21',
                \GoPay\Definition\Payment\VatRate::RATE_4
            ),
        ];
    }

    /**
     * Get delivery / shipping item
     *
     * @param ShippingItem $shipping
     * @return array
     */
    protected function getDeliveryItem(ShippingItem $shipping): array
    {
        return [
            'type' => \GoPay\Definition\Payment\PaymentItemType::DELIVERY,
            'name' => $shipping->getTitle(),
            'amount' => $shipping->getAmount(),
            'count' => 1,
            'vat_rate' => config(
                'madstore-gopay.vat.0',
                \GoPay\Definition\Payment\VatRate::RATE_1
            ),
        ];
    }

    /**
     * Get payer info
     *
     * @param HasOrderContactData $order
     * @return array
     */
    protected function getPayerData(HasPayerData $model): array
    {
        return [
            // 'default_payment_instrument' => config('madstore-gopay.default_payment_instrument'),
            // 'allowed_payment_instruments' => config('madstore-gopay.allowed_payment_instruments'),
            // 'default_swift' => config('madstore-gopay.default_swift'),
            // 'allowed_swifts' => config('madstore-gopay.allowed_swifts'),
            'contact' => [
                'first_name' => $model->getFirstName(),
                'last_name' => $model->getLastName(),
                'email' => $model->getEmail(),
                'phone_number' => $model->getPhoneNumber(),
                'city' => $model->getCity(),
                'street' => $model->getStreet(),
                'postal_code' => $model->getZipCode(),
                'country_code' => $model->country->getCountryIso3Code(),
            ]
        ];
    }

    /**
     * Get data for EET
     *
     * @return array
     */
    protected function getEET(): array
    {
        return [
            // 'eet' => [
            //     'celk_trzba' => 139951,
            //     'zakl_dan1' => 99160,
            //     'dan1' => 20830,
            //     'zakl_dan2' => 17358,
            //     'dan2' => 2603,
            //     'mena' => Currency::CZECH_CROWNS
            // ],
        ];
    }
}

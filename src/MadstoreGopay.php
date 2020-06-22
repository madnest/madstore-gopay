<?php

namespace Madnest\MadstoreGopay;

use Madnest\LaravelGopay\LaravelGopay;
use Madnest\Madstore\Payment\Contracts\HasPayerInfo;
use Madnest\Madstore\Payment\Contracts\PaymentOption;
use Madnest\Madstore\Payment\Contracts\Purchasable;
use Madnest\Madstore\Payment\Contracts\PurchasableItem;
use Madnest\Madstore\Payment\PaymentResponse;
use Madnest\Madstore\Shipping\Contracts\ShippingItem;

class MadstoreGopay implements PaymentOption
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
     * @return PaymentResponse
     */
    public function createPayment(Purchasable $purchasable, array $params = [], array $options = []): PaymentResponse
    {
        $response = $this->gopay->createPayment($this->getParams($purchasable, $params, $options));

        if ($response->hasSucceed()) {
            return $this->successfullResponse($response);
        }

        return $this->errorResponse($response);
    }

    protected function successfullResponse(\GoPay\Http\Response $response)
    {
        return (new PaymentResponse($response->statusCode, $response->json['state']))
            ->setOrderNumber($response->json['order_number'])
            ->setAmount($response->json['amount'])
            ->setCurrency($response->json['currency'])
            ->setPayer($response->json['payer'])
            ->setRedirectUrl($response->json['gw_url'])
            ->setRedirect($response->json['gw_url'] ? true : false)
            ->setErrors([]);
    }

    protected function errorResponse(\GoPay\Http\Response $response)
    {
        return (new PaymentResponse($response->statusCode, $response->json['state']))
            ->setErrors($response->json['errors']);
    }

    protected function getParams(Purchasable $model, array $params = [], array $options = []): array
    {
        return array_merge(
            [
                'payer' => $this->mapPayerInfo($model->getPayerInfo()),
                'amount' => $model->getFinalAmount(),
                'currency' => $model->getCurrency(),
                'order_number' => $model->getVarSymbol(),
                'order_description' => $model->getUUID(),
                'items' => $this->getItems($model),
                'additional_params' => $params,
                'lang' => config("madstore-gopay.{$model->getLanguage()}"),
                'callback' => [
                    'return_url' => config('madstore-gopay.return_url'),
                    'notification_url' => config('madstore-gopay.notification_url'),
                ],
            ],
            // If EET, then get EET data
            config('madstore-gopay.eet') ? $this->getEET($model) : [],
            $options,
        );
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
     * @param HasPayerInfo $model
     * @return array
     */
    protected function mapPayerInfo(HasPayerInfo $model): array
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
                'country_code' => $model->getCountryIso3Code(),
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

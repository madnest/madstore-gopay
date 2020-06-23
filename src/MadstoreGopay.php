<?php

namespace Madnest\MadstoreGopay;

use Illuminate\Support\Str;
use Madnest\LaravelGopay\LaravelGopay;
use Madnest\Madstore\Payment\Contracts\HasPayerInfo;
use Madnest\Madstore\Payment\Contracts\PaymentOption;
use Madnest\Madstore\Payment\Contracts\Purchasable;
use Madnest\Madstore\Payment\Contracts\PurchasableItem;
use Madnest\Madstore\Payment\Enums\PaymentStatus;
use Madnest\Madstore\Payment\PaymentResponse;
use Madnest\Madstore\Shipping\Contracts\ShippableItem;
use Madnest\MadstoreGopay\Items\PurchaseItem;
use Madnest\MadstoreGopay\Items\ShippingItem;

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
        // dd($this->mapParams($purchasable, $params, $options));
        $response = $this->gopay->createPayment($this->mapParams($purchasable, $params, $options));

        if ($response->hasSucceed()) {
            return $this->successResponse($response);
        }

        return $this->errorResponse($response);
    }

    public function getStatus($id): PaymentResponse
    {
        $response = $this->gopay->getStatus($id);

        if ($response->hasSucceed()) {
            return $this->statusResponse($response);
        }

        return $this->errorResponse($response);
    }

    protected function successResponse(\GoPay\Http\Response $response)
    {
        return $this
            ->newPaymentResponse($response->statusCode, $response->json['state'])
            ->setOrderNumber($response->json['order_number'])
            ->setAmount($response->json['amount'])
            ->setCurrency($response->json['currency'])
            ->setPayer($response->json['payer'])
            ->setRedirectUrl($response->json['gw_url'])
            ->setRedirect($this->shouldRedirect())
            ->setErrors([]);
    }

    protected function errorResponse(\GoPay\Http\Response $response)
    {
        if (isset($response->json['errors'])) {
            return $this
                ->newPaymentResponse($response->statusCode, PaymentStatus::ERROR)
                ->setErrors($response->json['errors']);
        }

        return $this
            ->newPaymentResponse($response->statusCode, PaymentStatus::ERROR)
            ->setErrors([
                'message' => (string) $response
            ]);
    }

    protected function statusResponse(\GoPay\Http\Response $response)
    {
        return $this
            ->newPaymentResponse($response->statusCode, $response->json['state'])
            ->setRedirectUrl($response->json['gw_url']);
    }

    protected function newPaymentResponse(int $statusCode, string $paymentStatus): PaymentResponse
    {
        return new PaymentResponse($statusCode, $paymentStatus);
    }

    protected function mapParams(Purchasable $model, array $params = [], array $options = []): array
    {
        $params = [
            'payer' => $this->mapPayerInfo($model->getPayerInfo()),
            'amount' => $model->getFinalAmount(),
            'currency' => $model->getCurrency(),
            'order_number' => $model->getVarSymbol(),
            'order_description' => $model->getUUID(),
            'items' => $this->mapItems($model),
            'additional_params' => $params,
            'lang' => config("madstore-gopay.{$model->getLanguage()}"),
            'callback' => [
                'return_url' => config('madstore-gopay.return_url'),
                'notification_url' => config('madstore-gopay.notification_url'),
            ],
        ];

        return array_merge($params, config('madstore-gopay.eet') ? $this->getEET($model) : [], $options);
    }

    /**
     * Loop through purchasable items and map them,
     * possible to also add discount items
     * and delivery item
     *
     * @param Purchasable $model
     * @return array
     */
    protected function mapItems(Purchasable $model): array
    {
        if ($model->getItems()->isEmpty()) {
            throw new \InvalidArgumentException('There are no items to be purchased.');
        }

        $purchaseItems = $model->getItems()->map(fn ($item) => $this->getPurchaseItem($item))->toArray();
        $shippingItem = $this->getShippingItem($model->getShippableItem());

        return array_merge($purchaseItems, [$shippingItem]);
    }

    /**
     * Get PurchaseItem
     *
     * @param PurchasableItem $item
     * @return array
     */
    protected function getPurchaseItem(PurchasableItem $item): array
    {
        $purchaseItem = new PurchaseItem(
            $item->getTitle(),
            $item->getAmount(),
            $item->getQuantity(),
            $item->getVATRate()
        );

        $additionalKeys = ['url', 'ean'];

        foreach ($additionalKeys as $key) {
            $key = Str::camel($key);
            $getMethod = 'get' . $key;
            if (method_exists($item, $getMethod)) {
                $setMethod = 'set' . $key;

                $purchaseItem->$setMethod($item->$getMethod());
            }
        }

        return $purchaseItem->toArray();
    }

    /**
     * Get ShippingItem
     *
     * @param ShippingItem $shipping
     * @return array
     */
    protected function getShippingItem(ShippableItem $shipping): array
    {
        $shippingItem = new ShippingItem($shipping->getTitle(), $shipping->getAmount());

        return $shippingItem->toArray();
    }

    /**
     * Map payer info
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
     * Map data for EET
     *
     * @return array
     */
    protected function mapEET(): array
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

    /**
     * Determine wether GoPay should redirect
     *
     * @return boolean
     */
    protected function shouldRedirect(): bool
    {
        return config('madstore-gopay.inline') ? false : true;
    }
}

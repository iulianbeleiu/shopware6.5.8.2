<?php declare(strict_types=1);

namespace Swag\Order\Test\Service;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\CartPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Shopware\Core\Checkout\Cart\Tax\Struct\CalculatedTaxCollection;
use Shopware\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Shopware\Core\Checkout\Order\Aggregate\OrderDelivery\OrderDeliveryStates;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Checkout\Order\OrderStates;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\CashRoundingConfig;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Test\TestCaseBase\SalesChannelApiTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\StateMachine\Loader\InitialStateIdLoader;
use Shopware\Core\Test\TestDefaults;
use Swag\Order\Service\OrderService;

class OrderServiceFiltersTest extends TestCase
{
    use IntegrationTestBehaviour;
    use SalesChannelApiTestBehaviour;

    public static function getName(): string
    {
        return 'Test order service filters';
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->class = $this->createMock(OrderService::class);
        $this->orderRepository = $this->getContainer()->get('order.repository');
    }

    public function testCountryFilterOrders(): void
    {
        $context = Context::createDefaultContext();
        $customerId = $this->createCustomer();

        $this->createOrder($customerId, $context, new \DateTimeImmutable('yesterday'), '018d82ae116d738ca176fdd8a7a0db18');
        $this->createOrder($customerId, $context, new \DateTimeImmutable('yesterday'), '018d82ae116d738ca176fdd8a7a0db18');
        $this->createOrder($customerId, $context, new \DateTimeImmutable('yesterday'), '018d82ae116d738ca176fdd8a7a0db18');
        $this->createOrder($customerId, $context, new \DateTimeImmutable('yesterday'), '018d82ae116d738ca176fdd8a7a0db18');

        $this->createOrder($customerId, $context, new \DateTimeImmutable('yesterday'), '018d82ae11f47328a70ce3c3719c4725');
        $this->createOrder($customerId, $context, new \DateTimeImmutable('yesterday'), '018d82ae11f47328a70ce3c3719c4725');
        $this->createOrder($customerId, $context, new \DateTimeImmutable('yesterday'), '018d82ae11f47328a70ce3c3719c4725');

        $orderService = $this->getContainer()->get(OrderService::class);

        $filtersOrdersInRomania = ['numberOfDays' => 7, 'countryId' => '018d82ae11f47328a70ce3c3719c4725'];
        $ordersInRomaniaCount = $orderService->getOrders($filtersOrdersInRomania, Context::createDefaultContext())->count();

        $filtersOrdersInGermany = ['numberOfDays' => 7, 'countryId' => '018d82ae116d738ca176fdd8a7a0db18'];
        $ordersInGermanyCount = $orderService->getOrders($filtersOrdersInGermany, Context::createDefaultContext())->count();

        static::assertEquals(4, $ordersInGermanyCount);
        static::assertEquals(3, $ordersInRomaniaCount);
    }

    private function createOrder(string $customerId, Context $context, \DateTimeImmutable $orderDateTime, string $countryId): OrderEntity
    {
        $orderId = Uuid::randomHex();
        $stateId = $this->getContainer()->get(InitialStateIdLoader::class)->get(OrderStates::STATE_MACHINE);
        $billingAddressId = Uuid::randomHex();

        $order = [
            'id' => $orderId,
            'itemRounding' => json_decode(json_encode(new CashRoundingConfig(2, 0.01, true), \JSON_THROW_ON_ERROR), true, 512, \JSON_THROW_ON_ERROR),
            'totalRounding' => json_decode(json_encode(new CashRoundingConfig(2, 0.01, true), \JSON_THROW_ON_ERROR), true, 512, \JSON_THROW_ON_ERROR),
            'orderNumber' => Uuid::randomHex(),
            'orderDateTime' => $orderDateTime->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            'price' => new CartPrice(10, 10, 10, new CalculatedTaxCollection(), new TaxRuleCollection(), CartPrice::TAX_STATE_NET),
            'shippingCosts' => new CalculatedPrice(10, 10, new CalculatedTaxCollection(), new TaxRuleCollection()),
            'orderCustomer' => [
                'customerId' => $customerId,
                'email' => 'test@example.com',
                'salutationId' => $this->getValidSalutationId(),
                'firstName' => 'Max',
                'lastName' => 'Mustermann',
            ],
            'stateId' => $stateId,
            'paymentMethodId' => $this->getValidPaymentMethodId(),
            'currencyId' => Defaults::CURRENCY,
            'currencyFactor' => 1.0,
            'salesChannelId' => TestDefaults::SALES_CHANNEL,
            'billingAddressId' => $billingAddressId,
            'addresses' => [
                [
                    'id' => $billingAddressId,
                    'salutationId' => $this->getValidSalutationId(),
                    'firstName' => 'Max',
                    'lastName' => 'Mustermann',
                    'street' => 'Ebbinghoff 10',
                    'zipcode' => '48624',
                    'city' => 'Schöppingen',
                    'countryId' => $countryId,
                ],
            ],
            'lineItems' => [
                [
                    'id' => Uuid::randomHex(),
                    'identifier' => Uuid::randomHex(),
                    'quantity' => 1,
                    'label' => 'label',
                    'type' => LineItem::CREDIT_LINE_ITEM_TYPE,
                    'price' => new CalculatedPrice(200, 200, new CalculatedTaxCollection(), new TaxRuleCollection()),
                    'priceDefinition' => new QuantityPriceDefinition(200, new TaxRuleCollection(), 2),
                ],
            ],
            'deliveries' => [
                [
                    'stateId' => $this->getContainer()->get(InitialStateIdLoader::class)->get(OrderDeliveryStates::STATE_MACHINE),
                    'shippingMethodId' => $this->getValidShippingMethodId(),
                    'shippingCosts' => new CalculatedPrice(10, 10, new CalculatedTaxCollection(), new TaxRuleCollection()),
                    'shippingDateEarliest' => date(\DATE_ISO8601),
                    'shippingDateLatest' => date(\DATE_ISO8601),
                    'shippingOrderAddress' => [
                        'salutationId' => $this->getValidSalutationId(),
                        'firstName' => 'Max',
                        'lastName' => 'Mustermann',
                        'zipcode' => '80333',
                        'city' => 'Schöppingen',
                        'street' => 'street',
                        'country' => [
                            'id' => $countryId,
                        ],
                    ],
                ],
            ],
            'context' => '{}',
            'payload' => '{}',
        ];

        $this->orderRepository->upsert([$order], $context);
        $order = $this->orderRepository->search(new Criteria([$orderId]), $context);

        return $order->first();
    }
}

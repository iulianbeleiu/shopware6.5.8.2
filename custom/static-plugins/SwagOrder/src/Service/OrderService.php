<?php declare(strict_types=1);

namespace Swag\Order\Service;

use Shopware\Core\Checkout\Order\OrderCollection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;

class OrderService implements OrderServiceInterface
{
    public function __construct(private readonly EntityRepository $orderRepository)
    {
    }

    public function getOrders(array $filters, Context $context): OrderCollection
    {
        return $this->orderRepository->search(
            $this->createCriteria($filters),
            Context::createDefaultContext()
        )->getEntities();
    }

    private function createCriteria(array $filters): Criteria
    {
        $criteria = new Criteria();

        $criteria->addAssociation('lineItems');
        $criteria->addAssociation('salesChannel');
        $criteria->addAssociation('transactions.stateMachineState');

        if (!empty($filters['numberOfDays'])) {
            $dateTimeFormat = (new \DateTimeImmutable(sprintf('-%s days', $filters['numberOfDays'])))
                ->format(Defaults::STORAGE_DATE_TIME_FORMAT);

            $criteria->addFilter(new RangeFilter(
                'orderDateTime',
                [
                    RangeFilter::GTE => $dateTimeFormat,
                ]
            ));
        }

        if (!empty($filters['countryId'])) {
            $criteria->addFilter(new EqualsFilter('deliveries.shippingOrderAddress.countryId', $filters['countryId']));
        }

	    if (!empty($filters['limit'])) {
		    $criteria->setLimit((int) $filters['limit']);
	    }

        return $criteria;
    }

    public function formatOrdersAsArray(OrderCollection $orders): array
    {
        $ordersArray = [];
        foreach ($orders as $order) {
            $orderDate = $order->getOrderDateTime()->format('Y-m-d H:i:s');
            $customerName = sprintf('%s %s', $order->getOrderCustomer()->getFirstName(), $order->getOrderCustomer()->getLastName());

            $ordersArray[] = [
                'orderNumber' => $order->getOrderNumber(),
                'orderDate' => $orderDate,
                'customerName' => $customerName,
                'totalAmount' => $order->getAmountTotal(),
                'lineItemsCount' => $order->getLineItems()->count(),
                'salesChannel' => $order->getSalesChannel()->getName(),
                'paymentStatus' => $order->getTransactions()->last()->getStateMachineState()->getName(),
            ];
        }

        return $ordersArray;
    }
}

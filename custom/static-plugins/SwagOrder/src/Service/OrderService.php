<?php declare(strict_types=1);

namespace Swag\Order\Service;

use Shopware\Core\Checkout\Order\OrderCollection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
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

        if (!empty($filters['number-of-days'])) {
            $dateTimeFormat = (new \DateTimeImmutable(sprintf('-%s days', $filters['number-of-days'])))
                ->format(Defaults::STORAGE_DATE_TIME_FORMAT);

            $criteria->addFilter(new RangeFilter(
                'orderDateTime',
                [
                    RangeFilter::GTE => $dateTimeFormat,
                ]
            ));
        }

        if (!empty($filters['limit'])) {
            $criteria->setLimit((int) $filters['limit']);
        }

        //		$criteria->addFields(['orderNumber']);

        return $criteria;
    }
}

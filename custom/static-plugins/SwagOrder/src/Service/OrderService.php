<?php declare(strict_types=1);

namespace Swag\Order\Service;

use Shopware\Core\Checkout\Order\OrderCollection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Symfony\Component\HttpFoundation\InputBag;

class OrderService implements OrderServiceInterface
{
    public function __construct(private readonly EntityRepository $orderRepository)
    {
    }

    public function getOrders(InputBag $filters, Context $context): OrderCollection
    {
        return $this->orderRepository->search(
            $this->createCriteria($filters),
            Context::createDefaultContext()
        )->getEntities();
    }

    private function createCriteria(InputBag $filters): Criteria
    {
        $criteria = new Criteria();

        if ($filters->get('number-of-days')) {
            $dateTimeFormat = (new \DateTimeImmutable(sprintf('-%s days', $filters->get('number-of-days'))))
                ->format(Defaults::STORAGE_DATE_TIME_FORMAT);

            $criteria->addFilter(new RangeFilter(
                'orderDateTime',
                [
                    RangeFilter::GTE => $dateTimeFormat,
                ]
            ));
        }

        if ($filters->get('limit')) {
            $criteria->setLimit((int) $filters->get('limit'));
        }

        //		$criteria->addFields(['orderNumber']);

        return $criteria;
    }
}

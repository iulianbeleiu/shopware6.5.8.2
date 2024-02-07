<?php declare(strict_types=1);

namespace Swag\Order\Service;

use Shopware\Core\Checkout\Order\OrderCollection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;

class OrderExporter implements OrderExporterInterface
{
    public function __construct(private readonly EntityRepository $orderRepository)
    {
    }

    public function export(int $numberOfDays, Context $context): OrderCollection
    {
        $dateTimeFormat = (new \DateTimeImmutable(sprintf('-%s days', $numberOfDays)))
            ->format(Defaults::STORAGE_DATE_TIME_FORMAT);

        $criteria = new Criteria();
        $criteria->addFilter(new RangeFilter(
            'orderDateTime',
            [
                RangeFilter::GTE => $dateTimeFormat,
            ]
        ));

        return $this->orderRepository->search($criteria, Context::createDefaultContext())->getEntities();
    }
}

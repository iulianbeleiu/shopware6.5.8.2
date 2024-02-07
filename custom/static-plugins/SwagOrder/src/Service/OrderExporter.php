<?php declare(strict_types=1);

namespace Swag\Order\Service;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;

class OrderExporter implements OrderExporterInterface
{

	public function __construct(private readonly EntityRepository $orderRepository)
	{
	}


	public function export(int $numberOfDays, Context $context): EntitySearchResult
	{
		$criteria = new Criteria();
		$criteria->addFilter(new RangeFilter(
			'orderDateTime',
			[
				RangeFilter::GTE => (new \DateTime(sprintf('-%s days', $numberOfDays)))->format(\DateTimeInterface::ATOM),
			]
		));

		return $this->orderRepository->search($criteria, Context::createDefaultContext());
	}
}
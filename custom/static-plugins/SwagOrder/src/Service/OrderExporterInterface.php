<?php declare(strict_types=1);

namespace Swag\Order\Service;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;

interface OrderExporterInterface
{
    public function export(
	    int $numberOfDays,
        Context $context
    ): EntitySearchResult;
}

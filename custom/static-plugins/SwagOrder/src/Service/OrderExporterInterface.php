<?php declare(strict_types=1);

namespace Swag\Order\Service;

use Shopware\Core\Checkout\Order\OrderCollection;
use Shopware\Core\Framework\Context;

interface OrderExporterInterface
{
    public function export(
        int $numberOfDays,
        Context $context
    ): OrderCollection;
}

<?php declare(strict_types=1);

namespace Swag\Order\Service;

use Shopware\Core\Checkout\Order\OrderCollection;
use Shopware\Core\Framework\Context;
use Symfony\Component\HttpFoundation\InputBag;

interface OrderServiceInterface
{
    public function getOrders(
        InputBag $filters,
        Context $context
    ): OrderCollection;
}

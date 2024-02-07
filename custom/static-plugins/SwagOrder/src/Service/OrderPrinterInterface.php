<?php declare(strict_types=1);

namespace Swag\Order\Service;

use Shopware\Core\Checkout\Order\OrderCollection;
use Symfony\Component\Console\Output\OutputInterface;

interface OrderPrinterInterface
{
    public function print(
        OrderCollection $orders,
        OutputInterface $output
    ): void;
}

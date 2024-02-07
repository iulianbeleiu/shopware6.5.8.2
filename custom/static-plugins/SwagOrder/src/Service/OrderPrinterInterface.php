<?php declare(strict_types=1);

namespace Swag\Order\Service;

use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Symfony\Component\Console\Output\OutputInterface;

interface OrderPrinterInterface
{
    public function print(
        EntitySearchResult $orders,
        OutputInterface $output
    ): void;
}

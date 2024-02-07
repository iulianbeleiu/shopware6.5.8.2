<?php declare(strict_types=1);

namespace Swag\Order\Command;

use Shopware\Core\Framework\Context;
use Swag\Order\Service\OrderExporterInterface;
use Swag\Order\Service\OrderPrinterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OrderListCommand extends Command
{
    protected static $defaultName = 'order:recent-orders';

    public function __construct(
        private readonly OrderExporterInterface $orderExporter,
        private readonly OrderPrinterInterface $orderPrinter
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Returns an order table with its order number & date, the name of the customer and the total amount');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $orders = $this->orderExporter->export(7, Context::createDefaultContext());

        $this->orderPrinter->print($orders, $output);

        return Command::SUCCESS;
    }
}

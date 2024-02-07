<?php declare(strict_types=1);

namespace Swag\Order\Command;

use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Swag\Order\Service\OrderExporterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OrderListCommand extends Command
{
    protected static $defaultName = 'order:recent-orders';

    public function __construct(
        private readonly OrderExporterInterface $orderExporter
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

        $table = new Table($output);
        $table->setHeaders(['Order Number', 'Order Date', 'Customer', 'Total']);

        /** @var OrderEntity $order */
        foreach ($orders as $order) {
            $orderNumber = $order->getOrderNumber();
            $orderDate = $order->getOrderDateTime()->format('Y-m-d H:i:s');

            $customerName = sprintf('%s %s', $order->getOrderCustomer()->getFirstName(), $order->getOrderCustomer()->getLastName());
            $table->addRow([$orderNumber, $orderDate, $customerName, $order->getAmountTotal()]);
        }

        $table->render();

        return Command::SUCCESS;
    }
}

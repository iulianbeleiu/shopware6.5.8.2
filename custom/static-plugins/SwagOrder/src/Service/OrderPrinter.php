<?php declare(strict_types=1);

namespace Swag\Order\Service;

use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class OrderPrinter implements OrderPrinterInterface
{
    public function print(EntitySearchResult $orders, OutputInterface $output): void
    {
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
    }
}

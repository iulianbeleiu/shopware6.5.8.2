<?php declare(strict_types=1);

namespace Swag\Order\Command;

use Shopware\Core\Framework\Context;
use Swag\Order\Service\OrderPrinterInterface;
use Swag\Order\Service\OrderServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'order:recent-orders',
    description: 'Returns an order table with its order number & date, the name of the customer and the total amount',
)]
class OrderListCommand extends Command
{
    public function __construct(
        private readonly OrderServiceInterface $orderService,
        private readonly OrderPrinterInterface $orderPrinter
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('number-of-days', InputArgument::REQUIRED, 'Number of days to display the orders');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $numberOfDays = $io->ask('Number of days', '7', function (string $numberOfDays): int {
            if (!is_numeric($numberOfDays)) {
                throw new \RuntimeException('You must type a number.');
            }

            return (int) $numberOfDays;
        });

        $filters = ['numberOfDays' => (int) $numberOfDays];
        $orders = $this->orderService->getOrders($filters, Context::createDefaultContext());

        $this->orderPrinter->print($orders, $output);

        return Command::SUCCESS;
    }
}

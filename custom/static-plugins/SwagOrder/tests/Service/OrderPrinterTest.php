<?php declare(strict_types=1);

namespace Swag\Order\Test\Service;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Swag\Order\Service\OrderPrinter;
use Swag\Order\Service\OrderPrinterInterface;

class OrderPrinterTest extends TestCase
{
    use IntegrationTestBehaviour;

    public static function getName(): string
    {
        return 'Test order printer';
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->class = $this->createMock(OrderPrinter::class);
    }

    public function testClassStructure(): void
    {
        static::assertInstanceOf(OrderPrinterInterface::class, $this->class);
        static::assertTrue(method_exists($this->class, 'print'));
    }
}

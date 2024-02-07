<?php declare(strict_types=1);

namespace Swag\Order\Test\Service;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Swag\Order\Service\OrderExporter;
use Swag\Order\Service\OrderExporterInterface;

class OrderExporterTest extends TestCase
{
    use IntegrationTestBehaviour;

    public static function getName(): string
    {
        return 'Test order eporter';
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->class = $this->createMock(OrderExporter::class);
    }

    public function testClassStructure(): void
    {
        static::assertInstanceOf(OrderExporterInterface::class, $this->class);
        static::assertTrue(method_exists($this->class, 'export'));
    }
}

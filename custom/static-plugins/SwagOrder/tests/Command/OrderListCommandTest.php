<?php declare(strict_types=1);

namespace Swag\Order\Test\Command;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Swag\Order\Command\OrderListCommand;
use Symfony\Component\Console\Command\Command;

class OrderListCommandTest extends TestCase
{
    use IntegrationTestBehaviour;

    public static function getName(): string
    {
        return 'Test order command';
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->class = $this->createMock(OrderListCommand::class);
    }

    public function testClassStructure(): void
    {
        static::assertInstanceOf(Command::class, $this->class);
        static::assertTrue(method_exists($this->class, 'execute'));
        static::assertTrue(method_exists($this->class, 'configure'));
    }
}

<?php declare(strict_types=1);

namespace Swag\BasicExample\Core\Content\TestTable;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(TestTableEntity $entity)
 * @method void set(string $key, TestTableEntity $entity)
 * @method TestTableEntity[] getIterator()
 * @method TestTableEntity[] getElements()
 * @method TestTableEntity|null get(string $key)
 * @method TestTableEntity|null first()
 * @method TestTableEntity|null last()
 */
class TestTableCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return TestTableEntity::class;
    }
}

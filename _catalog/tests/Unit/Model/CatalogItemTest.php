<?php declare(strict_types=1);

namespace Catalog\Tests\Unit\Model;

use Catalog\Application\Model\CatalogItem;
use Catalog\Application\Model\Command\AddItemToCatalog;
use Catalog\Application\Model\Event\CatalogItemWasAdded;
use Ecotone\Lite\EcotoneLite;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class CatalogItemTest extends TestCase
{
    public function test_adding_item_to_catalog(): void
    {
        $catalogItemId = Uuid::uuid4()->toString();
        $name = 'irrelevant';
        $description = 'description';

        /** Verifying published events by aggregate, after calling command */
        $this->assertEquals(
            [new CatalogItemWasAdded($catalogItemId, $name, $description)],
            EcotoneLite::bootstrapFlowTesting([CatalogItem::class])
                ->sendCommand(new AddItemToCatalog($catalogItemId, $name, $description))
                ->getRecordedEvents()
        );
    }

    public function test_adding_item_to_catalog_with_routingKey(): void
    {
        $catalogItemId = Uuid::uuid4()->toString();
        $name = 'irrelevant';
        $description = 'description';

        /** Verifying published events by aggregate, after calling command */
        $this->assertEquals(
            [new CatalogItemWasAdded($catalogItemId, $name, $description)],
            EcotoneLite::bootstrapFlowTesting([CatalogItem::class])
                ->sendCommandWithRoutingKey('catalog.add-catalog-item', new AddItemToCatalog($catalogItemId, $name, $description))
                ->getRecordedEvents()
        );
    }
}

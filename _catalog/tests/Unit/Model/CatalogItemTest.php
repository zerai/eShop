<?php declare(strict_types=1);

namespace Catalog\Tests\Unit\Model;

use Catalog\Application\Model\CatalogItem;
use Catalog\Application\Model\Command\AddItemToCatalog;
use Catalog\Application\Model\Command\IncreaseStock;
use Catalog\Application\Model\Event\AvailableStockWasIncreased;
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

    public function test_increasing_catalog_item_stock(): void
    {
        $catalogItemId = Uuid::uuid4()->toString();
        $name = 'irrelevant';
        $description = 'description';
        $quantity = 100;

        /** Verifying aggregate property, after calling command */
        $this->assertEquals(
            100,
            EcotoneLite::bootstrapFlowTesting([CatalogItem::class])
                ->withEventsFor($catalogItemId, CatalogItem::class, [
                    new AddItemToCatalog($catalogItemId, $name, $description),
                ])
                ->sendCommand(new IncreaseStock($catalogItemId, $quantity))
                ->getAggregate(CatalogItem::class, $catalogItemId)
                ->availableStock()
        );
    }

    public function test_increasing_catalog_item_stock_with_message_flow(): void
    {
        $catalogItemId = Uuid::uuid4()->toString();
        $name = 'irrelevant';
        $description = 'description';
        $quantity = 100;

        /** Verifying published events by aggregate, after calling command */
        $this->assertEquals(
            [new AvailableStockWasIncreased($catalogItemId, $quantity)],
            EcotoneLite::bootstrapFlowTesting([CatalogItem::class])
                ->withEventsFor($catalogItemId, CatalogItem::class, [
                    new AddItemToCatalog($catalogItemId, $name, $description),
                ])
                ->sendCommand(new IncreaseStock($catalogItemId, $quantity))
                ->getRecordedEvents()
        );
    }
}

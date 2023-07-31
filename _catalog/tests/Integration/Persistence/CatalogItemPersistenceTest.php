<?php declare(strict_types=1);

namespace Catalog\Tests\Integration\Persistence;

use Catalog\Application\Model\CatalogItem;
use Catalog\Application\Model\Command\AddItemToCatalog;
use Catalog\Application\Model\Command\IncreaseStock;
use Catalog\Tests\Integration\EcotoneDbConnectionConf;
use Ecotone\Lite\EcotoneLite;
use Enqueue\Dbal\DbalConnectionFactory;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CatalogItemPersistenceTest extends TestCase
{
    public function test_aggregate_catalog_item_persistence(): void
    {
        $catalogItemId = Uuid::uuid4()->toString();
        $name = 'irrelevant';
        $description = 'description';
        $quantity = 100;

        $this->assertEquals(
            100,
            EcotoneLite::bootstrapFlowTestingWithEventStore(
                [CatalogItem::class],
                [
                    DbalConnectionFactory::class => new DbalConnectionFactory(EcotoneDbConnectionConf::databaseDns()),
                ],
                runForProductionEventStore: true
            )
                ->withEventsFor($catalogItemId, CatalogItem::class, [
                    new AddItemToCatalog($catalogItemId, $name, $description),
                ])
                ->deleteEventStream('catalog_item_stream')
                ->sendCommand(new IncreaseStock($catalogItemId, $quantity))
                ->getAggregate(CatalogItem::class, $catalogItemId)
                ->availableStock()
        );
    }
}

<?php declare(strict_types=1);

namespace Catalog\Tests\Integration\AclAdapter\ViewModel;

use Catalog\AclAdapter\ViewModel\CatalogItemProjectionUsingDocumentStore;
use Catalog\Application\Model\CatalogItem;
use Catalog\Application\Model\Event\CatalogItemWasAdded;
use Catalog\Tests\Integration\EcotoneDbConnectionConf;
use Ecotone\Lite\EcotoneLite;
use Enqueue\Dbal\DbalConnectionFactory;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CatalogItemProjectionUsingDocumentStoreTest extends TestCase
{
    public function testProjectionFirstEvent(): void
    {
        self::markTestIncomplete();
        $catalogItemId = Uuid::uuid4()->toString();
        $name = 'irrelevant';
        $description = 'description';

        self::assertEquals(
            ['foo', 'bar'],
            EcotoneLite::bootstrapFlowTestingWithEventStore(
                // 1. Setting projection and aggregate that we want to resolve
                [CatalogItemProjectionUsingDocumentStore::class, CatalogItem::class],
                [
                    //DbalConnectionFactory::class => new DbalConnectionFactory(EcotoneDbConnectionConf::databaseDns()),
                    new CatalogItemProjectionUsingDocumentStore(),

                ],
                //runForProductionEventStore: true
            )
                ->deleteEventStream('catalog_item_stream')

                // 2. Providing initial events to run projection on
                ->withEventsFor($catalogItemId, CatalogItem::class, [
                    new CatalogItemWasAdded($catalogItemId, $name, $description),
                ])
                //->deleteProjection('prj_catalog_item_with_ds')
                // 3. Triggering projection
                ->triggerProjection('prj_catalog_item_with_ds')
                // 4. Runing query on projection to validate the state
                //->sendQueryWithRouting(CatalogItemProjectionUsingDocumentStore::GET_ITEM_BASE_DATA, $catalogItemId)
                ->sendQueryWithRouting(CatalogItemProjectionUsingDocumentStore::GET_ALL_ITEM)
        );
    }
}

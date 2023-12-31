<?php declare(strict_types=1);

namespace Catalog\Tests\Integration\AclAdapter\ViewModel;

use Catalog\AclAdapter\ViewModel\CatalogItemProjection;

use Catalog\Application\Model\CatalogItem;
use Catalog\Application\Model\Event\CatalogItemWasAdded;
use Catalog\Tests\Integration\EcotoneDbConnectionConf;
use Ecotone\Lite\EcotoneLite;
use Ecotone\Lite\Test\FlowTestSupport;
use Enqueue\Dbal\DbalConnectionFactory;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CatalogItemProjectionTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->getTestSupport()
            ->deleteEventStream(CatalogItem::STREAM_NAME)
            ->initializeProjection(CatalogItemProjection::NAME)
            ->resetProjection(CatalogItemProjection::NAME);
    }

    public function test_projection_query_get_item_by_id(): void
    {
        $catalogItemId = Uuid::uuid4()->toString();
        $name = 'irrelevant';
        $description = 'description';

        $expectedProjectionQueryResult = [
            [
                'id' => $catalogItemId,
                'name' => $name,
                'description' => $description,
            ],
        ];

        self::assertEquals(
            $expectedProjectionQueryResult,
            $this->getTestSupport()
                //->deleteEventStream(CatalogItem::STREAM_NAME)
                //->resetProjection(CatalogItemProjection::NAME)
                // 2. Providing initial events to run projection on
                ->withEventsFor($catalogItemId, CatalogItem::class, [
                    new CatalogItemWasAdded($catalogItemId, $name, $description),
                    //new CatalogItemWasAdded(Uuid::uuid4()->toString(), $name, $description),
                ])

                // 3. Triggering projection
                ->triggerProjection(CatalogItemProjection::NAME)
                // 4. Runing query on projection to validate the state
                //->sendQueryWithRouting(CatalogItemProjectionUsingDocumentStore::GET_ITEM_BASE_DATA, $catalogItemId)
                ->sendQueryWithRouting(CatalogItemProjection::GET_ITEM_BY_ID, $catalogItemId)
        );
    }

    private function getTestSupport(): FlowTestSupport
    {
        return EcotoneLite::bootstrapFlowTestingWithEventStore(
            // 1. Setting projection and aggregate that we want to resolve
            [CatalogItemProjection::class, CatalogItem::class],
            [
                DbalConnectionFactory::class => new DbalConnectionFactory(EcotoneDbConnectionConf::databaseDns()),
                new CatalogItemProjection((self::bootKernel())->getContainer()->get('doctrine.dbal.default_connection')),

            ],
            runForProductionEventStore: true
        );
    }
}

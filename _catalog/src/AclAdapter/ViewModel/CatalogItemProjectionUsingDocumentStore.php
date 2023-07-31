<?php declare(strict_types=1);

namespace Catalog\AclAdapter\ViewModel;

use Catalog\Application\Model\CatalogItem;
use Catalog\Application\Model\Event\CatalogItemWasAdded;
use Ecotone\EventSourcing\Attribute\Projection;
use Ecotone\Messaging\Store\Document\DocumentStore;
use Ecotone\Modelling\Attribute\EventHandler;
use Ecotone\Modelling\Attribute\QueryHandler;

#[Projection('prj_' . self::NAME, CatalogItem::class)]
class CatalogItemProjectionUsingDocumentStore
{
    public const NAME = "catalog_item_with_ds";

    public const GET_ITEM_BASE_DATA = 'getCatalogItemBaseData';

    public const GET_ALL_ITEM = 'get_all_catalog_item';

    #[EventHandler]
    public function whenCatalogItemWasAdded(CatalogItemWasAdded $event, DocumentStore $documentStore): void
    {
        $catalogItem = $documentStore->findDocument(self::NAME, $event->catalogItemId);

        if ($catalogItem === null) {
            $catalogItem = [];
        }

        //$catalogItem[$event->catalogItemId] = $event->getPrice();

        $documentStore->upsertDocument(
            self::NAME,
            $event->catalogItemId,
            [
                'catalogItemId' => $event->catalogItemId,
                'name' => $event->name,
                'description' => $event->description,
            ]
        );
    }

    #[QueryHandler(self::GET_ITEM_BASE_DATA)]
    public function getCatalogItemBaseData(string $catalogItemId, DocumentStore $documentStore): array
    {
        return $documentStore->getDocument(self::NAME, $catalogItemId);
    }

    #[QueryHandler(self::GET_ALL_ITEM)]
    public function getAllCatalogItem(array $data, DocumentStore $documentStore): array
    {
        return $documentStore->getAllDocuments(self::NAME);
    }
}

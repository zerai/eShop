<?php declare(strict_types=1);

namespace Catalog\AclAdapter\ViewModel;

use Catalog\Application\Model\CatalogItem;
use Catalog\Application\Model\Event\CatalogItemWasAdded;
use Doctrine\DBAL\Connection;
use Ecotone\EventSourcing\Attribute\Projection;
use Ecotone\EventSourcing\Attribute\ProjectionDelete;
use Ecotone\EventSourcing\Attribute\ProjectionInitialization;
use Ecotone\EventSourcing\Attribute\ProjectionReset;
use Ecotone\Modelling\Attribute\EventHandler;
use Ecotone\Modelling\Attribute\QueryHandler;

#[Projection(self::NAME, CatalogItem::class)]
class CatalogItemProjection
{
    public const NAME = "prj_catalog_item";

    public const GET_ITEM_BY_ID = 'get_catalog_item_by_id';

    public function __construct(
        private Connection $connection
    ) {
    }

    #[EventHandler]
    public function onCatalogItemWasAdded(CatalogItemWasAdded $event): void
    {
        $this->connection->insert(self::NAME, [
            'id' => $event->catalogItemId,
            'name' => $event->name,
            'description' => $event->description,
        ]);
    }

    #[QueryHandler(self::GET_ITEM_BY_ID)]
    public function getCatalogItemById(string $catalogItemId): array
    {
        //return [];
        //        return [
        //            "ticket" => $this->connection->executeQuery(<<<SQL
        //    SELECT * FROM prj_catalog_item WHERE id = :item_id
        //SQL, ["item_id" => $catalogItemId])->fetchAllAssociative()
        //        ];

        return $this->connection->executeQuery(
            <<<SQL
    SELECT * FROM prj_catalog_item WHERE id = :item_id
SQL,
            [
                "item_id" => $catalogItemId,
            ]
        )->fetchAllAssociative()
        ;
    }

    #[ProjectionInitialization]
    public function initialization(): void
    {
        $this->connection->executeStatement(
            <<<SQL
                CREATE TABLE IF NOT EXISTS prj_catalog_item (
                id VARCHAR(36) PRIMARY KEY,
                name VARCHAR(255),
                description VARCHAR(255)
                )
            SQL
        );
    }

    #[ProjectionReset]
    public function reset(): void
    {
        $this->connection->executeStatement(
            <<<SQL
    DELETE FROM prj_catalog_item
SQL
        );
    }

    #[ProjectionDelete]
    public function delete(): void
    {
        $this->connection->executeStatement(
            <<<SQL
    DROP TABLE prj_catalog_item
SQL
        );
    }
}

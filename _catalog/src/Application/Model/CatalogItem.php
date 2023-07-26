<?php declare(strict_types=1);

namespace Catalog\Application\Model;

use Catalog\Application\Model\Command\AddItemToCatalog;
use Catalog\Application\Model\Event\CatalogItemWasAdded;
use Ecotone\Modelling\Attribute\AggregateIdentifier;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\Attribute\EventSourcingAggregate;
use Ecotone\Modelling\Attribute\EventSourcingHandler;
use Ecotone\Modelling\WithAggregateVersioning;

#[EventSourcingAggregate]
class CatalogItem
{
    public const ADD_CATALOG_ITEM = 'catalog.add-catalog-item';

    use WithAggregateVersioning;

    #[AggregateIdentifier]
    private string $catalogItemId;

    private string $name;

    private string $description;

    private int $price;

    private string $pictureFileName;

    private string $pictureUri;

    private int $catalogTypeId;

    //TODO
    //private CatalogType $catalogType;

    private int $catalogBrandId;

    //TODO
    //private CatalogBrand $catalogBrand;

    private int $availableStock;

    private int $restockThreshold;

    private int $maxStockThreshold;

    public bool $onOrder;

    #[CommandHandler(self::ADD_CATALOG_ITEM)]
    #[CommandHandler()]
    public static function add(AddItemToCatalog $command): array
    {
        return [new CatalogItemWasAdded($command->catalogItemId, $command->name, $command->description)];
    }

    #[EventSourcingHandler]
    public function applyCustomerBasketWasCreated(CatalogItemWasAdded $event): void
    {
        $this->catalogItemId = $event->catalogItemId;
        $this->name = $event->name;
        $this->description = $event->description;
    }
}

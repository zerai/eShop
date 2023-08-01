<?php declare(strict_types=1);

namespace Catalog\Application\Model;

use Catalog\Application\Model\Command\AddItemToCatalog;
use Catalog\Application\Model\Command\DecreaseStock;
use Catalog\Application\Model\Command\IncreaseStock;
use Catalog\Application\Model\Event\AvailableStockWasDecreased;
use Catalog\Application\Model\Event\AvailableStockWasIncreased;
use Catalog\Application\Model\Event\CatalogItemWasAdded;
use Ecotone\EventSourcing\Attribute\Stream;
use Ecotone\Modelling\Attribute\AggregateIdentifier;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\Attribute\EventSourcingAggregate;
use Ecotone\Modelling\Attribute\EventSourcingHandler;
use Ecotone\Modelling\WithAggregateVersioning;

#[EventSourcingAggregate]
#[Stream(self::STREAM_NAME)]
class CatalogItem
{
    public const STREAM_NAME = 'catalog_item_stream';

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

    private int $availableStock = 0;

    private int $restockThreshold;

    private int $maxStockThreshold;

    public bool $onOrder;

    #[CommandHandler()]
    #[CommandHandler(self::ADD_CATALOG_ITEM)]
    public static function add(AddItemToCatalog $command): array
    {
        return [new CatalogItemWasAdded($command->catalogItemId, $command->name, $command->description)];
    }

    #[CommandHandler()]
    public static function addStock(IncreaseStock $command): array
    {
        return [new AvailableStockWasIncreased($command->catalogItemId(), $command->quantity())];
    }

    #[CommandHandler()]
    public static function removeStock(DecreaseStock $command): array
    {
        return [new AvailableStockWasDecreased($command->catalogItemId(), $command->quantity())];
    }

    public function id(): string
    {
        return $this->catalogItemId;
    }

    public function availableStock(): int
    {
        return $this->availableStock;
    }

    #[EventSourcingHandler]
    public function applyCatalogItemWasAdded(CatalogItemWasAdded $event): void
    {
        $this->catalogItemId = $event->catalogItemId;
        $this->name = $event->name;
        $this->description = $event->description;
    }

    #[EventSourcingHandler]
    public function applyAvailableStockWasIncreased(AvailableStockWasIncreased $event): void
    {
        $this->catalogItemId = $event->catalogItemId();
        $this->availableStock += $event->incrementFactor();
    }

    #[EventSourcingHandler]
    public function applyAvailableStockWasDecreased(AvailableStockWasDecreased $event): void
    {
        $this->catalogItemId = $event->catalogItemId();
        $this->availableStock -= $event->decrementFactor();
    }
}

<?php declare(strict_types=1);

namespace Catalog\Application\Model\Event;

class AvailableStockWasIncreased
{
    public function __construct(
        private string $catalogItemId,
        private int $incrementFactor
    ) {
    }

    public function catalogItemId(): string
    {
        return $this->catalogItemId;
    }

    public function incrementFactor(): int
    {
        return $this->incrementFactor;
    }
}

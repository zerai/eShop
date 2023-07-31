<?php declare(strict_types=1);

namespace Catalog\Application\Model\Event;

class AvailableStockWasDecreased
{
    public function __construct(
        private string $catalogItemId,
        private int $decrementFactor
    ) {
    }

    public function catalogItemId(): string
    {
        return $this->catalogItemId;
    }

    public function decrementFactor(): int
    {
        return $this->decrementFactor;
    }
}

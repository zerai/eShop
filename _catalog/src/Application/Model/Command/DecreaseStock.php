<?php declare(strict_types=1);

namespace Catalog\Application\Model\Command;

class DecreaseStock
{
    public function __construct(
        private string $catalogItemId,
        private int $quantity
    ) {
    }

    public function catalogItemId(): string
    {
        return $this->catalogItemId;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}

<?php declare(strict_types=1);

namespace Catalog\Application\Model\Command;

class AddItemToCatalog
{
    public function __construct(
        public string $catalogItemId,
        public string $name,
        public string $description
    ) {
    }
}

<?php declare(strict_types=1);

namespace Catalog\Application\Model\Event;

class CatalogItemWasAdded
{
    public function __construct(
        public string $catalogItemId,
        public string $name,
        public string $description
    ) {
    }
}

<?php declare(strict_types=1);

namespace Basket\Application\Model\Event;

class CustomerBasketWasCreated
{
    public function __construct(
        public string $basketId,
        public string $buyerId,
        public array $items
    ) {
    }
}

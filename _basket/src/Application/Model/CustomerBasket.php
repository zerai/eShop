<?php declare(strict_types=1);

namespace Basket\Application\Model;

use Basket\Application\Model\Command\CreateCustomerBasket;
use Basket\Application\Model\Event\CustomerBasketWasCreated;
use Ecotone\Modelling\Attribute\AggregateIdentifier;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\Attribute\EventSourcingAggregate;
use Ecotone\Modelling\Attribute\EventSourcingHandler;
use Ecotone\Modelling\WithAggregateVersioning;

#[EventSourcingAggregate]
class CustomerBasket
{
    public const CREATE_CUSTOMER_BASKET = 'basket.create-customer-basket';

    use WithAggregateVersioning;

    #[AggregateIdentifier]
    private string $basketId;

    private string $buyerId;

    private array $items = [];

    #[CommandHandler(self::CREATE_CUSTOMER_BASKET)]
    #[CommandHandler()]
    public static function register(CreateCustomerBasket $command): array
    {
        return [new CustomerBasketWasCreated($command->basketId, $command->buyerId, $command->items)];
    }

    #[EventSourcingHandler]
    public function applyCustomerBasketWasCreated(CustomerBasketWasCreated $event): void
    {
        $this->basketId = $event->basketId;
        $this->buyerId = $event->buyerId;
        $this->items = $event->items;
    }
}

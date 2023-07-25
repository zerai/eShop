<?php declare(strict_types=1);

namespace Basket\Tests\Unit\Model;

use Basket\Application\Model\Command\CreateCustomerBasket;
use Basket\Application\Model\CustomerBasket;
use Basket\Application\Model\Event\CustomerBasketWasCreated;
use Ecotone\Lite\EcotoneLite;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class BasketTest extends TestCase
{
    public function test_adding_product_to_basket(): void
    {
        $basketId = Uuid::uuid4()->toString();
        $buyerId = Uuid::uuid4()->toString();

        /** Verifying published events by aggregate, after calling command */
        $this->assertEquals(
            [new CustomerBasketWasCreated($basketId, $buyerId, [])],
            EcotoneLite::bootstrapFlowTesting([CustomerBasket::class])
                ->sendCommand(new CreateCustomerBasket($basketId, $buyerId, []))
                ->getRecordedEvents()
        );
    }

    public function test_adding_product_to_basket_with_routingKey(): void
    {
        $basketId = Uuid::uuid4()->toString();
        $buyerId = Uuid::uuid4()->toString();

        /** Verifying published events by aggregate, after calling command */
        $this->assertEquals(
            [new CustomerBasketWasCreated($basketId, $buyerId, [])],
            EcotoneLite::bootstrapFlowTesting([CustomerBasket::class])
                ->sendCommandWithRoutingKey('basket.create-customer-basket', new CreateCustomerBasket($basketId, $buyerId, []))
                ->getRecordedEvents()
        );
    }

    public function test_skipping_product_if_already_added(): void
    {
        self::markTestIncomplete();
        $basketId = Uuid::uuid4()->toString();
        $buyerId = Uuid::uuid4()->toString();

        $this->assertEquals(
            [],
            EcotoneLite::bootstrapFlowTesting([CustomerBasket::class], [])
                ->sendCommand(new CreateCustomerBasket($basketId, $buyerId, []))
                ->discardRecordedMessages()
                ->sendCommand(new CreateCustomerBasket($basketId, $buyerId, []))
                ->getRecordedEvents()
        );
    }
}

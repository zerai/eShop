<?php declare(strict_types=1);

namespace Basket\Application\Model;

interface BasketRepository
{
    public function updateBasket(CustomerBasket $basket);
}

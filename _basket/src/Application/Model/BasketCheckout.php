<?php declare(strict_types=1);

namespace Basket\Application\Model;

class BasketCheckout
{
    public string $city;

    public string $street;

    public string $state;

    public string $country;

    public string $zipCode;

    public string $cardNumber;

    public string $cardHolderName;

    public \DateTimeImmutable $cardExpiration;

    public string $cardSecurityNumber;

    public int $cardTypeId;

    public string $buyer;

    public string $requestId;
}

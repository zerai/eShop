<?php declare(strict_types=1);

namespace App\Tests\Functional\Security;

use Ramsey\Uuid\Nonstandard\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NewAccountControllerTest extends WebTestCase
{
    public function testUserRegistrationEndpoint(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/auth/register',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'userId' => Uuid::uuid4()->toString(),
                'email' => 'afakemail@example.it',
                'password' => 'afakepassword',
            ], JSON_THROW_ON_ERROR)
        );

        self::assertResponseIsSuccessful();
    }
}

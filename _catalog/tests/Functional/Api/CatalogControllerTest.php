<?php declare(strict_types=1);

namespace Catalog\Tests\Functional\Api;

use Catalog\Tests\Integration\EcotoneDbConnectionConf;
use Ecotone\Lite\EcotoneLite;
use Ecotone\Lite\Test\FlowTestSupport;
use Ecotone\Modelling\CommandBus;
use Enqueue\Dbal\DbalConnectionFactory;
use IdentityAccess\Application\Model\Identity\Command\RegisterUser;
use IdentityAccess\Application\Model\Identity\ReadModel\UserListProjection;
use IdentityAccess\Application\Model\Identity\User;
use IdentityAccess\Infrastructure\Authentication\SecurityUser;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;

class CatalogControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    private string $path = '/api/catalog/items/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->getTestSupport($this->client->getContainer()->get('doctrine.dbal.default_connection'))
            ->deleteEventStream(User::STREAM_NAME)
            ->initializeProjection(UserListProjection::NAME)
            ->resetProjection(UserListProjection::NAME);
    }

    /**
     * Create a client with a default Authorization header.
     * return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function getAuthenticatedClient(string $userEmail = null, string $password = null)
    {
        $passwordHasher = $this->client->getContainer()->get('security.user_password_hasher');
        \assert(($passwordHasher instanceof UserPasswordHasher));

        $userId = Uuid::uuid4()->toString();
        $userEmail = $userEmail ?: 'test_' . substr($userId, 10) . '@example.com';
        $userPassword = $password ?: Uuid::uuid4()->toString();

        $this->createUser($userId, $userEmail, SecurityUser::encryptPassword($userPassword, $passwordHasher));

        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'username' => $userEmail,
                'password' => $userPassword,
            ], JSON_THROW_ON_ERROR)
        );

        dd($this->client->getResponse()->getContent());
        $data = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $this->client;
    }

    public function test_api_show_catalog_item()
    {
        //self::markTestIncomplete();
        $catalogItemId = '1';
        $client = $this->getAuthenticatedClient('zerai@example.com', 'pippopippo');
        $client->request('GET', sprintf("%s%s", $this->path, $catalogItemId));

        self::assertResponseIsSuccessful();
    }

    private function createUser($userId, $email = null, $password = null): void
    {
        $commandBus = $this->client->getContainer()->get(CommandBus::class);
        \assert(($commandBus instanceof CommandBus));

        $commandBus->send(new RegisterUser($email, $password, $userId));
    }

    private function getTestSupport($dbalConnection): FlowTestSupport
    {
        return EcotoneLite::bootstrapFlowTestingWithEventStore(
            // 1. Setting projection and aggregate that we want to resolve
            [UserListProjection::class, User::class],
            [
                DbalConnectionFactory::class => new DbalConnectionFactory(EcotoneDbConnectionConf::databaseDns()),
                //new UserListProjection((self::bootKernel())->getContainer()->get('doctrine.dbal.default_connection')),
                new UserListProjection($dbalConnection),

            ],
            runForProductionEventStore: true
        );
    }
}

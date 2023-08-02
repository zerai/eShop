<?php declare(strict_types=1);

namespace IdentityAccess\Tests\Integration\ReadModel;

use Catalog\Tests\Integration\EcotoneDbConnectionConf;
use Ecotone\Lite\EcotoneLite;
use Ecotone\Lite\Test\FlowTestSupport;
use Enqueue\Dbal\DbalConnectionFactory;
use IdentityAccess\Application\Model\Identity\Event\UserWasRegistered;
use IdentityAccess\Application\Model\Identity\ReadModel\UserListProjection;
use IdentityAccess\Application\Model\Identity\User;
use IdentityAccess\Infrastructure\Authentication\SecurityUser;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \IdentityAccess\Application\Model\Identity\ReadModel\UserListProjection
 */
class UserListProjectionTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->getTestSupport()
            ->deleteEventStream(User::STREAM_NAME)
            ->initializeProjection(UserListProjection::NAME)
            ->resetProjection(UserListProjection::NAME);
    }

    public function test_projection_query_get_security_user(): void
    {
        $userId = Uuid::uuid4()->toString();
        $email = 'irrelevant@example.com';
        $password = 'irrelevant';

        $expectedProjectionQueryResult = SecurityUser::createFromReadModel($email, $password);

        self::assertEquals(
            $expectedProjectionQueryResult,
            $this->getTestSupport()
                // 2. Providing initial events to run projection on
                ->withEventsFor($userId, User::class, [
                    new UserWasRegistered($userId, $email, $password),
                ])
                // 3. Triggering projection
                ->triggerProjection(UserListProjection::NAME)
                // 4. Running query on projection to validate the state
                ->sendQueryWithRouting(UserListProjection::GET_SECURITY_USER, $email)
        );
    }

    public function test_projection_query_get_user_list(): void
    {
        $userId = Uuid::uuid4()->toString();
        $email = 'irrelevant@example.com';
        $password = 'irrelevant';

        $otherUserId = Uuid::uuid4()->toString();
        $otherEmail = 'other@example.com';
        $otherPassword = 'irrelevant';

        $expectedProjectionQueryResult = [
            [
                'user_id' => $userId,
                'email' => $email,
                'password' => $password,

            ],
            [
                'user_id' => $otherUserId,
                'email' => $otherEmail,
                'password' => $otherPassword,

            ],
        ];

        self::assertEquals(
            $expectedProjectionQueryResult,
            $this->getTestSupport()
                // 2. Providing initial events to run projection on
                ->withEventsFor($userId, User::class, [
                    new UserWasRegistered($userId, $email, $password),
                    new UserWasRegistered($otherUserId, $otherEmail, $otherPassword),
                ])
                // 3. Triggering projection
                ->triggerProjection(UserListProjection::NAME)
                // 4. Running query on projection to validate the state
                ->sendQueryWithRouting(UserListProjection::GET_USER_LIST)
        );
    }

    private function getTestSupport(): FlowTestSupport
    {
        return EcotoneLite::bootstrapFlowTestingWithEventStore(
            // 1. Setting projection and aggregate that we want to resolve
            [UserListProjection::class, User::class],
            [
                DbalConnectionFactory::class => new DbalConnectionFactory(EcotoneDbConnectionConf::databaseDns()),
                new UserListProjection((self::bootKernel())->getContainer()->get('doctrine.dbal.default_connection')),

            ],
            runForProductionEventStore: true
        );
    }
}

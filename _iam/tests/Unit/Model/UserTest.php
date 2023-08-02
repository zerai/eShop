<?php declare(strict_types=1);

namespace IdentityAccess\Tests\Unit\Model;

use Ecotone\Lite\EcotoneLite;
use Ecotone\Lite\Test\FlowTestSupport;
use IdentityAccess\Application\Model\Identity\Event\UserWasRegistered;
use IdentityAccess\Application\Model\Identity\User;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class UserTest extends TestCase
{
    public function test_verify_user_properties_on_user_registration(): void
    {
        $expectedUserId = $userId = Uuid::uuid4()->toString();
        $expectedEmail = $email = 'user.' . $userId . '@example.com';
        $hashedPassword = Uuid::uuid4()->toString();

        /** Verifying aggregate id property, after calling command */
        $this->assertEquals(
            $expectedUserId,
            $retrievedUserId = $this->getTestSupport()
                ->withEventsFor($userId, User::class, [
                    new UserWasRegistered($userId, $email, $hashedPassword),
                ])
                ->getAggregate(User::class, $userId)
                ->id(),
            sprintf("ERROR: expected User::id() '%s', got: %s", $email, $retrievedUserId)
        );

        /** Verifying aggregate email property, after calling command */
        $this->assertEquals(
            $expectedEmail,
            $retrievedEmail = $this->getTestSupport()
                ->withEventsFor($userId, User::class, [
                    new UserWasRegistered($userId, $email, $hashedPassword),
                ])
                ->getAggregate(User::class, $userId)
                ->email(),
            sprintf("ERROR: expected User::email() '%s', got: %s", $email, $retrievedEmail)
        );
    }

    private function getTestSupport(): FlowTestSupport
    {
        return EcotoneLite::bootstrapFlowTesting([User::class]);
    }
}

<?php declare(strict_types=1);

namespace IdentityAccess\Tests\Unit\Model;

use Ecotone\Lite\EcotoneLite;
use IdentityAccess\Application\Model\Identity\Event\UserWasRegistered;
use IdentityAccess\Application\Model\Identity\User;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class UserTest extends TestCase
{
    public function test_adding_new_user(): void
    {
        $userId = Uuid::uuid4()->toString();
        $email = Uuid::uuid4()->toString();
        $hashedPassword = Uuid::uuid4()->toString();

        /** Verifying aggregate id property, after calling command */
        $this->assertEquals(
            $userId,
            EcotoneLite::bootstrapFlowTesting([User::class])
                ->withEventsFor($userId, User::class, [
                    new UserWasRegistered($userId, $email, $hashedPassword),
                ])
                ->getAggregate(User::class, $userId)
                ->id()
        );
    }
}

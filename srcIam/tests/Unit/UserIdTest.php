<?php declare(strict_types=1);

namespace IdentityAccess\Tests\Unit;

use IdentityAccess\Application\Model\Identity\UserId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IdentityAccess\Application\Model\Identity\UserId
 */
class UserIdTest extends TestCase
{
    private const UUID = 'fcb5d0b3-5fd9-45ef-a002-57fecfdc5dd0';

    private const OTHER_UUID = '1352cf79-ec96-4efa-af92-e0cae57c7951';

    public function testAttibuteAfterConstruct(): void
    {
        $sut = UserId::fromString(self::UUID);

        self::assertSame(self::UUID, $sut->__tostring());
    }

    public function testValidation(): void
    {
        self::markTestIncomplete('TODO: Add UserId validation');
        UserId::fromString('xx');
    }

    public function testSuccessComparation(): void
    {
        $sut = UserId::fromString(self::UUID);

        $otherSut = UserId::fromString(self::UUID);

        self::assertTrue($sut->equals($otherSut));
    }

    public function testFailComparation(): void
    {
        $sut = UserId::fromString(self::UUID);

        $otherSut = UserId::fromString(self::OTHER_UUID);

        self::assertFalse($sut->equals($otherSut));
    }
}

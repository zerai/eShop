<?php declare(strict_types=1);

namespace IdentityAccess\Application\Model\Identity\ReadModel;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Ecotone\EventSourcing\Attribute\Projection;
use Ecotone\EventSourcing\Attribute\ProjectionDelete;
use Ecotone\EventSourcing\Attribute\ProjectionInitialization;
use Ecotone\EventSourcing\Attribute\ProjectionReset;
use Ecotone\Modelling\Attribute\EventHandler;
use Ecotone\Modelling\Attribute\QueryHandler;
use IdentityAccess\Application\Model\Identity\Event\UserPasswordWasChanged;
use IdentityAccess\Application\Model\Identity\Event\UserWasRegistered;
use IdentityAccess\Application\Model\Identity\User;
use IdentityAccess\Infrastructure\Authentication\SecurityUser;

#[Projection(self::NAME, User::STREAM_NAME)]
class UserListProjection
{
    public const NAME = "prj_user_list";

    public const GET_USER_LIST = "getUserList";

    public const GET_SECURITY_USER = "getSecurityUser";

    public function __construct(
        private Connection $connection
    ) {
    }

    #[EventHandler]
    public function addUser(UserWasRegistered $event, array $metadata): void
    {
        $this->connection->insert(self::NAME, [
            'user_id' => $event->getUserId(),
            'email' => $event->getEmail(),
            'password' => $event->getHashedPassword(),
        ]);
    }

    #[EventHandler]
    public function changePassword(UserPasswordWasChanged $event, array $metadata): void
    {
        $this->connection->update(self::NAME, [
            "password" => $event->getPassword(),
        ], [
            "user_id" => $event->getUserId(),
        ]);
    }

    #[QueryHandler(self::GET_USER_LIST)]
    public function getUserList(): array
    {
        try {
            return $this->connection->executeQuery(
                <<<SQL
                    SELECT * FROM prj_user_list
                SQL
            )->fetchAllAssociative();
        } catch (TableNotFoundException) {
            return [];
        }
    }

    #[QueryHandler(self::GET_SECURITY_USER)]
    public function getSecurityUser(string $securityIdentifier): SecurityUser
    {
        $userData = $this->connection->executeQuery(
            <<<SQL
                SELECT email, password FROM prj_user_list WHERE email = :email
            SQL,
            [
                "email" => $securityIdentifier,
            ]
        )->fetchAllAssociative()[0];

        return SecurityUser::createFromReadModel($userData['email'], $userData['password']);
    }

    #[ProjectionInitialization]
    public function initialization(): void
    {
        $this->connection->executeStatement(
            <<<SQL
                CREATE TABLE IF NOT EXISTS prj_user_list (
                    user_id VARCHAR(36) PRIMARY KEY,
                    email VARCHAR(125),
                    password VARCHAR(200)
                )
            SQL
        );
    }

    #[ProjectionReset]
    public function reset(): void
    {
        $this->connection->executeStatement(
            <<<SQL
                DELETE FROM prj_user_list
            SQL
        );
    }

    #[ProjectionDelete]
    public function delete(): void
    {
        $this->connection->executeStatement(
            <<<SQL
                DROP TABLE prj_user_list
            SQL
        );
    }
}

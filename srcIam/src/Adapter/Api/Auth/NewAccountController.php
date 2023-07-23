<?php declare(strict_types=1);

namespace IdentityAccess\Adapter\Api\Auth;

use Ecotone\Modelling\CommandBus;
use IdentityAccess\Application\Model\Identity\Command\RegisterUser;
use IdentityAccess\Application\Model\Identity\User;
use IdentityAccess\Infrastructure\Authentication\SecurityUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class NewAccountController extends AbstractController
{
    public function __construct(
        private CommandBus $commandBus,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    #[Route("/auth/register", name: 'auth_register', methods: ["POST"])]
    public function register(Request $request): Response
    {
        $payload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $command = new RegisterUser(
            $payload['email'],
            SecurityUser::encryptPassword($payload['password'], $this->passwordHasher),
            $payload['userId'],
        );

        $this->commandBus->sendWithRouting(User::REGISTER_USER, $command);

        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_NO_CONTENT);
        return $response;
    }
}

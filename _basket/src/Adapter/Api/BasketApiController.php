<?php declare(strict_types=1);

namespace Basket\Adapter\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/basket", name: 'api_basket')]
class BasketApiController extends AbstractController
{
    #[Route("/{id}", name: 'show', methods: ["GET"])]
    public function show(): Response
    {
        //TODO:
        // Status200OK

        return new JsonResponse();
    }

    #[Route("/checkout/{id}", name: 'checkout', methods: ["POST"])]
    public function checkout(): Response
    {
        //TODO:
        // Status202Accepted
        // Status400BadRequest

        return new JsonResponse();
    }

    #[Route("/{id}", name: 'delete', methods: ["DELETE"])]
    public function delete(): Response
    {
        //TODO:
        // Status200OK

        return new JsonResponse();
    }
}

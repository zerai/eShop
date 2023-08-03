<?php declare(strict_types=1);

namespace Catalog\Adapter\Api;

use Catalog\AclAdapter\ViewModel\CatalogItemProjection;
use Ecotone\Modelling\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/catalog', name: 'api_catalog_items_')]
class CatalogItemController extends AbstractController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    #[Route('/items/{id}', name: 'by_id', methods: ['GET'])]
    public function itemById(string $id): Response
    {
        /** @TODO VALIDATE $id -> Response::HTTP_BAD_REQUEST */
        $data = $this->queryBus->sendWithRouting(CatalogItemProjection::GET_ITEM_BY_ID, $id);

        return new JsonResponse(
            $data,
            Response::HTTP_OK
        );
    }
}

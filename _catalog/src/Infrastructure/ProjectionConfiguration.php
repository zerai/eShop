<?php declare(strict_types=1);

namespace Catalog\Infrastructure;

use Catalog\AclAdapter\ViewModel\CatalogItemProjection;
use Catalog\AclAdapter\ViewModel\CatalogItemProjectionUsingDocumentStore;
use Ecotone\EventSourcing\ProjectionRunningConfiguration;
use Ecotone\Messaging\Attribute\ServiceContext;

class ProjectionConfiguration
{
    #[ServiceContext]
    public function CatalogItemList()
    {
        return ProjectionRunningConfiguration::createPolling(CatalogItemProjection::NAME);
    }

    #[ServiceContext]
    public function CatalogItemWithDocumentStore()
    {
        return ProjectionRunningConfiguration::createPolling('prj_' . CatalogItemProjectionUsingDocumentStore::NAME);
    }
}

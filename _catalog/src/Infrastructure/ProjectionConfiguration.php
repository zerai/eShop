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
        /** remove comment for async configuration */
        //return ProjectionRunningConfiguration::createPolling(CatalogItemProjection::NAME);
        return [];
    }

    #[ServiceContext]
    public function CatalogItemWithDocumentStore()
    {
        return ProjectionRunningConfiguration::createPolling('prj_' . CatalogItemProjectionUsingDocumentStore::NAME);
    }
}

<?php

namespace App\Service;

use App\Enum\PermissionAction;
use Doctrine\ORM\EntityManagerInterface;

class EntityNameService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Returns an array of all entity names in the application.
     *
     * @return string[] Array of entity class names
     */
    public function getAllEntityNames(): array
    {
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $entityNames = [];

        foreach ($metadata as $entityMetadata) {
            $nameSpitted   = explode('\\', $entityMetadata->getName());
            $entityNames[] = end($nameSpitted);
        }

        return $entityNames;
    }

    /**
     * Getting all entity names and set them as PERMISSSIONS
     * @return array
     */
    public function getAllEntityNamesAsPermissions(): array
    {
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $entityNames = [];

        foreach ($metadata as $entityMetadata) {
            $nameSpitted   = explode('\\', $entityMetadata->getName());
            foreach (PermissionAction::cases() as $action) {
                $entityNames[] = sprintf('ROLE_%s_%s', strtoupper(end($nameSpitted)), $action->value);
            }
        }

        return $entityNames;
    }
}

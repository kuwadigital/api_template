<?php

namespace App\Entity\Security;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\Security\RolePermissionAssociationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RolePermissionAssociationRepository::class)]
#[ApiResource(
    description:'Main application role permission association entity representation. Exemple Admin has Permission to User and association is CREATE, PUT ....',
    operations:[
        new Get(
            security: 'is_granted("ROLE_ROLEPERMISSIONASSOCIATION_GET")'
        ),
        new GetCollection(
            security: 'is_granted("ROLE_ROLEPERMISSIONASSOCIATION_GETCOLLECTION")'
        ),
        new Post(
            security: 'is_granted("ROLE_ROLEPERMISSIONASSOCIATION_POST")'
        ),
        new Put(
            security: 'is_granted("ROLE_ROLEPERMISSIONASSOCIATION_PUT")'
        ),
        new Patch(
            security: 'is_granted("ROLE_ROLEPERMISSIONASSOCIATION_PATCH")'
        ),
        new Delete(
            security: 'is_granted("ROLE_ROLEPERMISSIONASSOCIATION_DELETE")'
        )
    ]
)]
class RolePermissionAssociation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Role>
     */
    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'rolePermissionAssociations')]
    private Collection $role;

    #[ORM\ManyToOne(inversedBy: 'rolePermissionAssociations')]
    private ?Permission $permission = null;

    #[ORM\Column(length: 255)]
    private ?string $permissionAction = null;

    public function __construct()
    {
        $this->role = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getRole(): Collection
    {
        return $this->role;
    }

    public function addRole(Role $role): static
    {
        if (!$this->role->contains($role)) {
            $this->role->add($role);
        }

        return $this;
    }

    public function removeRole(Role $role): static
    {
        $this->role->removeElement($role);

        return $this;
    }

    public function getPermission(): ?Permission
    {
        return $this->permission;
    }

    public function setPermission(?Permission $permission): static
    {
        $this->permission = $permission;

        return $this;
    }

    public function getPermissionAction(): ?string
    {
        return $this->permissionAction;
    }

    public function setPermissionAction(string $permissionAction): static
    {
        $this->permissionAction = $permissionAction;

        return $this;
    }
}

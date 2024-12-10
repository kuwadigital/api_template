<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RolePermissionAssociationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RolePermissionAssociationRepository::class)]
#[ApiResource]
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

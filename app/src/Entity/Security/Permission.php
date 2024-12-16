<?php

namespace App\Entity\Security;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\Security\PermissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
#[ApiResource(
    description:'Main application Permission entity representation. Use to repressent the permission on each object in the application. Example: a permission User, Role, Book....',
    operations:[
        new Get(
            security: 'is_granted("ROLE_PERMISSION_GET")'
        ),
        new GetCollection(
            security: 'is_granted("ROLE_PERMISSION_GETCOLLECTION")'
        ),
        new Post(
            security: 'is_granted("ROLE_PERMISSION_POST")'
        ),
        new Put(
            security: 'is_granted("ROLE_PERMISSION_PUT")'
        ),
        new Patch(
            security: 'is_granted("ROLE_PERMISSION_PATCH")'
        ),
        new Delete(
            security: 'is_granted("ROLE_PERMISSION_DELETE")'
        )
    ]
)]
class Permission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $entityName = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, RolePermissionAssociation>
     */
    #[ORM\OneToMany(targetEntity: RolePermissionAssociation::class, mappedBy: 'permission')]
    private Collection $rolePermissionAssociations;

    public function __construct()
    {
        $this->rolePermissionAssociations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntityName(): ?string
    {
        return $this->entityName;
    }

    public function setEntityName(string $entityName): static
    {
        $this->entityName = $entityName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, RolePermissionAssociation>
     */
    public function getRolePermissionAssociations(): Collection
    {
        return $this->rolePermissionAssociations;
    }

    public function addRolePermissionAssociation(RolePermissionAssociation $rolePermissionAssociation): static
    {
        if (!$this->rolePermissionAssociations->contains($rolePermissionAssociation)) {
            $this->rolePermissionAssociations->add($rolePermissionAssociation);
            $rolePermissionAssociation->setPermission($this);
        }

        return $this;
    }

    public function removeRolePermissionAssociation(RolePermissionAssociation $rolePermissionAssociation): static
    {
        if ($this->rolePermissionAssociations->removeElement($rolePermissionAssociation)) {
            // set the owning side to null (unless already changed)
            if ($rolePermissionAssociation->getPermission() === $this) {
                $rolePermissionAssociation->setPermission(null);
            }
        }

        return $this;
    }
}

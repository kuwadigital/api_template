<?php

namespace App\Entity\Security;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\Security\PermissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    description:'Main application Permission entity representation. Use to repressent the permission on each object in the application. Example: a permission User, Role, Book....',
    operations:[
        new Get(
            security: 'is_granted("ROLE_PERMISSION_GET")',
            normalizationContext: ['groups' => ['permission:item:get', 'default']]
        ),
        new GetCollection(
            security: 'is_granted("ROLE_PERMISSION_GET_COLLECTION")',
            normalizationContext: ['groups' => ['permission:collection:get', 'default']]
        ),
        new Post(
            security: 'is_granted("ROLE_PERMISSION_POST")',
            normalizationContext: ['groups' => ['permission:item:get']],
            denormalizationContext: ['groups' => ['permission:item:post']]
        ),
        new Put(
            security: 'is_granted("ROLE_PERMISSION_PUT")',
            normalizationContext: ['groups' => ['permission:item:get']],
            denormalizationContext: ['groups' => ['permission:item:put']]
        ),
        new Patch(
            security: 'is_granted("ROLE_PERMISSION_PATCH")',
            normalizationContext: ['groups' => ['permission:item:get']],
            denormalizationContext: ['groups' => ['permission:item:patch']]
        ),
        new Delete(
            security: 'is_granted("ROLE_PERMISSION_DELETE")'
        )
    ]
)]
class Permission
{
    /**
     * Add the traits for created At and Updated At
     */
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $entityName = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    /**
     * @var Collection<int, Role>
     */
    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'permissions')]
    private Collection $roles;

    #[ORM\Column(length: 255)]
    private ?string $permissionAction = null;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
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

    /**
     * @return Collection<int, Role>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): static
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
            $role->addPermission($this);
        }

        return $this;
    }

    public function removeRole(Role $role): static
    {
        if ($this->roles->removeElement($role)) {
            $role->removePermission($this);
        }

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

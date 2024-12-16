<?php

namespace App\Entity\Security;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use App\Repository\Security\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ApiResource(
    description:'Main application role entity representation. Use to manage all the access in the application.',
    operations:[
        new Get(
            security: 'is_granted("ROLE_ROLE_GET")'
        ),
        new GetCollection(
            security: 'is_granted("ROLE_ROLE_GETCOLLECTION")'
        ),
        new Post(
            security: 'is_granted("ROLE_ROLE_POST")'
        ),
        new Put(
            security: 'is_granted("ROLE_ROLE_PUT")'
        ),
        new Patch(
            security: 'is_granted("ROLE_ROLE_PATCH")'
        ),
        new Delete(
            security: 'is_granted("ROLE_ROLE_DELETE")'
        )
    ]
)]
#[ApiFilter(PropertyFilter::class)]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'appRoles')]
    private Collection $users;

    /**
     * @var Collection<int, RolePermissionAssociation>
     */
    #[ORM\ManyToMany(targetEntity: RolePermissionAssociation::class, mappedBy: 'role')]
    private Collection $rolePermissionAssociations;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->rolePermissionAssociations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addAppRole($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeAppRole($this);
        }

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
            $rolePermissionAssociation->addRole($this);
        }

        return $this;
    }

    public function removeRolePermissionAssociation(RolePermissionAssociation $rolePermissionAssociation): static
    {
        if ($this->rolePermissionAssociations->removeElement($rolePermissionAssociation)) {
            $rolePermissionAssociation->removeRole($this);
        }

        return $this;
    }
}

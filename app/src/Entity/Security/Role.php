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
use App\Entity\Traits\TimestampableTrait;
use App\Repository\Security\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    description:'Main application role entity representation. Use to manage all the access in the application.',
    operations:[
        new Get(
            security: 'is_granted("ROLE_ROLE_GET")',
            normalizationContext: ['groups' => ['role:item:get', 'default']]
        ),
        new GetCollection(
            security: 'is_granted("ROLE_ROLE_GET_COLLECTION")',
            normalizationContext: ['groups' => ['role:collection:get', 'default']]
        ),
        new Post(
            security: 'is_granted("ROLE_ROLE_POST")',
            normalizationContext: ['groups' => ['role:item:get']],
            denormalizationContext: ['groups' => ['role:item:post']]
        ),
        new Put(
            security: 'is_granted("ROLE_ROLE_PUT")',
            normalizationContext: ['groups' => ['role:item:get']],
            denormalizationContext: ['groups' => ['role:item:put']]
        ),
        new Patch(
            security: 'is_granted("ROLE_ROLE_PATCH")',
            normalizationContext: ['groups' => ['role:item:get']],
            denormalizationContext: ['groups' => ['role:item:patch']]
        ),
        new Delete(
            security: 'is_granted("ROLE_ROLE_DELETE")'
        )
    ]
)]
#[ApiFilter(PropertyFilter::class)]
class Role
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
    #[Assert\NotBlank()]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $description = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'appRoles')]
    private Collection $users;

    /**
     * @var Collection<int, Permission>
     */
    #[ORM\ManyToMany(targetEntity: Permission::class, inversedBy: 'roles')]
    private Collection $permissions;
    
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->permissions = new ArrayCollection();
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
     * @return Collection<int, Permission>
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): static
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
        }

        return $this;
    }

    public function removePermission(Permission $permission): static
    {
        $this->permissions->removeElement($permission);

        return $this;
    }

}

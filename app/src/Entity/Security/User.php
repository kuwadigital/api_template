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
use App\Repository\Security\UserRepository;
use App\State\UserHashPasswordStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
    description:'Main application user entity representation. First point of identification in the application.',
    operations:[
        new Get(
            security: 'is_granted("ROLE_USER_GET")',
            normalizationContext: ['groups' => ['user:item:get', 'default']]
        ),
        new GetCollection(
            security: "is_granted('ROLE_USER_GET_COLLECTION')",
            normalizationContext: ['groups' => ['user:collection:get', 'default']]
        ),
        new Post(
            security: 'is_granted("ROLE_USER_POST")',
            normalizationContext: ['groups' => ['user:item:get']],
            denormalizationContext: ['groups' => ['user:item:post']],
            processor: UserHashPasswordStateProcessor::class
        ),
        new Put(
            security: 'is_granted("ROLE_USER_PUT")',
            normalizationContext: ['groups' => ['user:item:get']],
            denormalizationContext: ['groups' => ['user:item:put']],
            processor: UserHashPasswordStateProcessor::class
        ),
        new Patch(
            security: 'is_granted("ROLE_USER_PATCH")',
            normalizationContext: ['groups' => ['user:item:get']],
            denormalizationContext: ['groups' => ['user:item:patch']],
            processor: UserHashPasswordStateProcessor::class
        ),
        new Delete(
            security: 'is_granted("ROLE_USER_DELETE")'
        )
    ]
)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['username'], message: 'It looks like another dragon took your username. ROAR!')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * Add the traits for created At and Updated At
     */
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'user:collection:get',
        'user:item:get',
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\Email()]
    #[Groups([
        'user:collection:get',
        'user:item:get',
        'user:item:post',
        'user:item:put',
        'user:item:patch',
    ])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups([
        'user:collection:get',
        'user:item:get',
        'user:item:post',
        'user:item:put',
        'user:item:patch',
    ])]
    private array $roles = [];

    /**
     * Roles given during API authentication with token
     * @var 
     */
    #[Groups([
        'user:collection:get',
        'user:item:get',
        'user:item:post',
        'user:item:put',
        'user:item:patch',
    ])]
    private ?array $accessTokenRoles = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[SerializedName('password')]
    #[Groups([
        'user:item:post',
        'user:item:put',
        'user:item:patch',
    ])]
    private ?string $plainPassword = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Groups([
        'user:collection:get',
        'user:item:get',
        'user:item:post',
        'user:item:put',
        'user:item:patch',
    ])]
    private ?string $username = null;

    /**
     * @var Collection<int, Role>
     */
    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
    #[Groups([
        'user:collection:get',
        'user:item:get',
        'user:item:post',
        'user:item:put',
        'user:item:patch',
    ])]
    private Collection $appRoles;

    /**
     * @var Collection<int, ApiToken>
     */
    #[ORM\OneToMany(targetEntity: ApiToken::class, mappedBy: 'ownedby', cascade: ['persist'], orphanRemoval: true)]
    #[Groups([
        'user:collection:get',
        'user:item:get',
        'user:item:post',
        'user:item:put',
        'user:item:patch',
    ])]
    private Collection $apiTokens;

    public function __construct()
    {
        $this->appRoles = new ArrayCollection();
        $this->apiTokens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {

        if ($this->accessTokenRoles == null) {
            
            /**
             * @infos: Si l'utilisateur ne passe pas le token, il n aura pas d'authorisations
             */
            $roles = $this->roles;

        } else {
        
            $roles = $this->accessTokenRoles;
        
        }
        
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getAppRoles(): Collection
    {
        return $this->appRoles;
    }

    public function addAppRole(Role $appRole): static
    {
        if (!$this->appRoles->contains($appRole)) {
            $this->appRoles->add($appRole);
        }

        return $this;
    }

    public function removeAppRole(Role $appRole): static
    {
        $this->appRoles->removeElement($appRole);

        return $this;
    }

    /**
     * @return Collection<int, ApiToken>
     */
    public function getApiTokens(): Collection
    {
        return $this->apiTokens;
    }

    public function addApiToken(ApiToken $apiToken): static
    {
        if (!$this->apiTokens->contains($apiToken)) {
            $this->apiTokens->add($apiToken);
            $apiToken->setOwnedby($this);
        }

        return $this;
    }

    public function removeApiToken(ApiToken $apiToken): static
    {
        if ($this->apiTokens->removeElement($apiToken)) {
            // set the owning side to null (unless already changed)
            if ($apiToken->getOwnedby() === $this) {
                $apiToken->setOwnedby(null);
            }
        }

        return $this;
    }

    #[SerializedName('password')]
    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @return string[]
     */
    public function getValidTokenStrings(): array
    {
        return $this->getApiTokens()
            ->filter(fn (ApiToken $token) => $token->isValid())
            ->map(fn (ApiToken $token) => $token->getToken())
            ->toArray()
        ;
    }

    /**
     * Calles during the authentication to select the valid token roles
     * @param array $scope
     * @return void
     */
    public function markAsTokenAuthenticated(array $scope)
    {
        $this->accessTokenRoles = $scope;
    }
}

<?php

namespace App\Entity\Security;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\Security\ApiTokenRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ApiTokenRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ApiToken
{
    /**
     * Add the traits for created At and Updated At
     */
    use TimestampableTrait;

    /**
     * The prefix of the generated token from the API: later we can let login from other ressources like github, facebook .....
     * @var string
     */
    private const PERSONAL_ACCESS_TOKEN_PREFIX = 'API_TOKEN_';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'apiTokens',  cascade: ['remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $ownedby = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'user:item:get'
    ])]
    private ?\DateTimeImmutable $expiresAt = null;

    #[ORM\Column(length: 100)]
    #[Groups([
        'user:collection:get',
        'user:item:get'
    ])]
    private ?string $token = null;

    #[ORM\Column]
    #[Groups([
        'user:item:get'
    ])]
    private array $scope = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $client_ip = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $client_user_agent = null;

    public function __construct(string $tokenType = self::PERSONAL_ACCESS_TOKEN_PREFIX){
        $this->token = $tokenType.bin2hex(random_bytes(32));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwnedby(): ?User
    {
        return $this->ownedby;
    }

    public function setOwnedby(?User $ownedby): static
    {
        $this->ownedby = $ownedby;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeImmutable $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getScope(): array
    {
        return $this->scope;
    }

    public function setScope(array $scope): static
    {
        $this->scope = $scope;

        return $this;
    }
    
    public function isValid(): bool
    {
        return $this->expiresAt === null || $this->expiresAt > new \DateTimeImmutable();
    }

    public function getClientIp(): ?string
    {
        return $this->client_ip;
    }

    public function setClientIp(?string $client_ip): static
    {
        $this->client_ip = $client_ip;

        return $this;
    }

    public function getClientUserAgent(): ?string
    {
        return $this->client_user_agent;
    }

    public function setClientUserAgent(?string $client_user_agent): static
    {
        $this->client_user_agent = $client_user_agent;

        return $this;
    }
}

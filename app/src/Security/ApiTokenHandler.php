<?php

namespace App\Security;

use App\Repository\Security\ApiTokenRepository;
use App\Service\ClientInfoService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class ApiTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private ApiTokenRepository $apiTokenRepository,
        protected EntityManagerInterface $entityManager,
        protected ClientInfoService $clientInfoService,
    )
    {
    }

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        $token = $this->apiTokenRepository->findOneBy([
            'token' => $accessToken,
            'client_ip' => $this->clientInfoService->getClientIp(),
            'client_user_agent' => $this->clientInfoService->getUserAgent()
        ]);

        if (!$token) {
            throw new BadCredentialsException();
        }

        if (!$token->isValid()) {
            throw new CustomUserMessageAuthenticationException('Token expired');
        }

        /**
         * Updating the token to be valid again for the next 30mins
         */
        // Create a DateTimeImmutable instance for "now"
        $now = new \DateTimeImmutable();

        // Add 30 minutes to the current time
        $expiresAt = $now->modify('+30 minutes');
        $token->setExpiresAt($expiresAt);
        $this->entityManager->persist($token);
        $this->entityManager->flush();
        
        /**
         * Adding token scopes to the user
         */
        $token->getOwnedBy()->markAsTokenAuthenticated($token->getScope());

        return new UserBadge($token->getOwnedBy()->getUserIdentifier());
    }
}
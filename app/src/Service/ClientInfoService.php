<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class ClientInfoService
{
    private RequestStack $requestStack;

    public function __construct(
        RequestStack $requestStack
    )
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Retourne l'adresse IP du client.
     */
    public function getClientIp(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();
        return $request ? $request->getClientIp() : null;
    }

    /**
     * Retourne l'agent utilisateur (User-Agent) du client.
     */
    public function getUserAgent(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();
        return $request ? $request->headers->get('User-Agent') : null;
    }

    /**
     * Retourne toutes les informations pertinentes sur le client.
     */
    public function getClientInfo(): array
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return [];
        }

        return [
            'ip' => $request->getClientIp(),
            'user_agent' => $request->headers->get('User-Agent')
        ];
    }
}

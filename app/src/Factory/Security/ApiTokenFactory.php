<?php

namespace App\Factory\Security;

use App\Entity\Security\ApiToken;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<ApiToken>
 */
final class ApiTokenFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return ApiToken::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'ownedby' => UserFactory::new(),
            'scope' => [
                'ROLE_USER_GET',
                'ROLE_USER_GET_COLLECTION',
                'ROLE_USER_POST',
                'ROLE_USER_PUT',
                'ROLE_USER_PATCH'
            ]
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(ApiToken $apiToken): void {})
        ;
    }
}

<?php

namespace App\DataFixtures;

use App\Factory\Security\RoleFactory;
use App\Factory\Security\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /**
         * Create a ROOT Role
         */
        RoleFactory::createOne([
            'name' => 'ROOT',
            'description' => 'Super User of the system'
        ]);

        /**
         * Creating the developper user with the role ROOT
         */
        UserFactory::createOne([
            'email' => 'api.template@dev.local',
            'password' => 'dev',
            'username' => 'API Developper',
            'appRoles' => [RoleFactory::random()]
        ]);
    }
}

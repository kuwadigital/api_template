<?php

namespace App\DataFixtures;

use App\Factory\ApiTokenFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /**
         * Creating the developper user
         */
        UserFactory::createOne([
            'email' => 'api.template@dev.local',
            'password' => 'dev',
            'username' => 'API Developper',
        ]);

        ApiTokenFactory::createMany(1, function () {
            return [
                'ownedby' => UserFactory::random()
            ];
        });
    }
}

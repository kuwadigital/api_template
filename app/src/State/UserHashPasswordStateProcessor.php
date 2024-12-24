<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Security\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserHashPasswordStateProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor, 
        private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?user
    {
        if ($data->getPlainPassword()) {
            $data->setPassword($this->userPasswordHasher->hashPassword($data, $data->getPlainPassword()));
        }
        
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}

<?php

namespace App\Controller;

use ApiPlatform\Metadata\IriConverterInterface;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class InitLoginController extends AbstractController
{
    #[Route('/init/login/form', name: 'app_init_login_form')]
    public function index() : Response
    {
        return $this->render('init_login/index.html.twig');
    }

    #[Route('/init/login', name: 'app_init_login', methods: [ 'POST' ])]
    public function login(IriConverterInterface $iriConverterInterface, #[CurrentUser] User $user = null) : Response
    {
        if(!$user){
            return $this->json([
                'error' => 'You need to be logged in to use the API. [POST REQUEST FOR LOGIN]'
            ], 401);
        }

        return $this->json(
            [
                'user' => $iriConverterInterface->getIriFromResource($user)
            ]
        );
    }

    #[Route('/init/logout', name: 'app_init_logout')]
    public function logout() : void
    {
    }
}

<?php

namespace App\Controller;

use ApiPlatform\Metadata\IriConverterInterface;
use App\Entity\Security\ApiToken;
use App\Entity\Security\User;
use App\Service\ClientInfoService;
use App\Service\EntityNameService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class InitLoginController extends AbstractController
{

    public function __construct(
        protected EntityNameService $entityNameService,
        protected EntityManagerInterface $entityManager,
        protected ClientInfoService $clientInfoService
    )
    {}


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

        /**
         * Getting the App Roles of the user
         */
        $appRoles = [];
        foreach($user->getAppRoles() as $role) {
            $appRoles[] = $role->getName();
        }
        /**
         * Generating the scope for the user
         */
        $tokenScope = [];

        if (in_array('ROOT', $appRoles)) {
            /**
             * The user is ROOT, we give all existing permissions
             */
            $tokenScope = $this->entityNameService->getAllEntityNamesAsPermissions();
            $tokenScope[] = 'ROLE_ROOT'; // adding the special role ROOT to assure some fonctionalities that schould only be made by a ROOT
            
        } else {
            /**
             * The User is not ROOT, getting the permissions from the roles
             */
            foreach($user->getAppRoles() as $role) {
                foreach($role->getPermissions() as $permission) {
                    if (in_array(sprintf('ROLE_%s_%s', strtoupper($permission->getEntityName()), $permission->getPermissionAction()), $tokenScope ) == false) {
                        $tokenScope[] = sprintf('ROLE_%s_%s', strtoupper($permission->getEntityName()), $permission->getPermissionAction());
                    }
                }
            }
        }

        /**
         * Delete all existing token and create a new one with all the acutal Roles and return it to the requester
         */
        foreach($user->getApiTokens() as $apiToken) {
            $user->removeApiToken($apiToken);
        }

        // Create a DateTimeImmutable instance for "now"
        $now = new \DateTimeImmutable();

        // Add 30 minutes to the current time
        $expiresAt = $now->modify('+30 minutes');

        $apiToken = new ApiToken();
        $apiToken->setExpiresAt($expiresAt);
        $apiToken->setScope($tokenScope);
        $apiToken->setClientIp($this->clientInfoService->getClientIp());
        $apiToken->setClientUserAgent($this->clientInfoService->getUserAgent());
        $user->addApiToken($apiToken);
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(
            [
                'user' => $iriConverterInterface->getIriFromResource($user),
                'tokens' => $user->getValidTokenStrings()
            ]
        );
    }

    #[Route('/init/logout', name: 'app_init_logout')]
    public function logout() : void
    {
    }
}

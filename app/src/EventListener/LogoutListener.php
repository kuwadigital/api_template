<?php 

namespace App\EventListener;

use App\Repository\Security\ApiTokenRepository;
use App\Service\ClientInfoService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;

class LogoutListener implements EventSubscriberInterface
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        private ApiTokenRepository $apiTokenRepository,
        protected ClientInfoService $clientInfoService
    )
    {
    }
    public function onLogout(LogoutEvent $event)
    {
        // Perform custom logic on logout
        // For example, log the logout action or perform cleanup tasks
        
        $token = $this->apiTokenRepository->findOneBy([
            'client_ip' => $this->clientInfoService->getClientIp(),
            'client_user_agent' => $this->clientInfoService->getUserAgent()
        ]);

        if (!$token) {
            return;
        }

        // You can access the user object via $event->getToken()->getUser()
        $user = $token->getOwnedBy();
        /**
         * Delete all existing token and create a new one with all the acutal Roles and return it to the requester
         */
        foreach($user->getApiTokens() as $apiToken) {
            $user->removeApiToken($apiToken);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        
        // Log the user logout (or add any other logic you want)
        // You can use your logger service here
        // $this->logger->info('User logged out: ' . $user->getUsername());

        // You could even redirect the user or return custom responses if needed
        // $event->setResponse(new Response('You are logged out'));
    }

    public static function getSubscribedEvents()
    {
        return [
            // Listen to the logout event
            LogoutEvent::class => 'onLogout',
        ];
    }
}
<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class MaintenanceListener
{
    public function onKernelRequest(RequestEvent $event)
    {
        $cache = new FilesystemAdapter();
        $maintenance = $cache->getItem('maintenance_mode')->get();

        if ($maintenance) {
            $response = new JsonResponse([
                'message' => 'Maintenance mode activated !',
            ], JsonResponse::HTTP_SERVICE_UNAVAILABLE);

            $event->setResponse($response);
        }
    }
}
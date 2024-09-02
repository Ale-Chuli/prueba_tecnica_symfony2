<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use App\Entity\Users;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $user = $event->getUser();

        if (!$user instanceof Users) {
            throw new \Exception('Unexpected user type');
        }

        $payload = $event->getData();

        $payload['password'] = $user->getPassword();

        $event->setData($payload);
    }
}

<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use App\Entity\Users;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $user = $event->getUser();

        //Checks if $user is type Users(App/Entity/Users)
        if (!$user instanceof Users) {
            throw new \Exception('Unexpected user type');
        }

        //Gets the JWT payload
        $payload = $event->getData();

        //Add the password to the JWT payload
        $payload['password'] = $user->getPassword();

        //Sets the new payload
        $event->setData($payload);
    }
}

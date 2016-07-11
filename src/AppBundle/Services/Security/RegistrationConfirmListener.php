<?php
namespace AppBundle\Services\Security;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\UserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationConfirmListener implements EventSubscriberInterface
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
                'fos_user.registration.confirmed' => 'onRegistrationConfirm'
        );
    }

    public function onRegistrationConfirm(GetResponseUserEvent $event)
    {
        $url = $this->router->generate('user_homepage');

        $event->setResponse(new RedirectResponse($url));
    }
}

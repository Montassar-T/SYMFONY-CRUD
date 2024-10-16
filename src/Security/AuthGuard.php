<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class AuthGuard
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function check(RequestEvent $event)
    {
        $request = $event->getRequest();
        
        // Allow access to the login page or other public resources
        if ($request->attributes->get('_route') === 'app_login' || $this->security->isGranted('IS_AUTHENTICATED_ANONYMOUSLY')) {
            return;
        }

        // Check if the user is authenticated
        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            // Redirect to login if not authenticated
            $event->setResponse(new RedirectResponse('/login'));
        }
    }
}


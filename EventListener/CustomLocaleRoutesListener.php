<?php
namespace ArtemFrolov\Bundle\LocaleSwitcherBundle\EventListener;

use ArtemFrolov\Bundle\LocaleSwitcherBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class CustomLocaleRoutesListener
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function onRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $currentRoute = $event->getRequest()->get('_route');
        $newRoute = $this->router->getCustomRouteName($currentRoute);
        if ($currentRoute !== $newRoute) {
            $url = $this->router->generate(
                $newRoute,
                $event->getRequest()->query->getIterator()->getArrayCopy()
            );
            $response = new RedirectResponse($url, 301);
            $event->setResponse($response);
        }
    }
}

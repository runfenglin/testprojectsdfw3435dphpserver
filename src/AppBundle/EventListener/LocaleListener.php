<?php
/**
 * Locale Listener
 * author: Haiping Lu
 */
namespace AppBundle\EventListener;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class LocaleListener
{
    public function _construct(Container $container)
    {
        $this->container = $container;
    }
    
    public function onKernelRequest(GetResponseEvent $event)
    {
        die('Event');
    }
}
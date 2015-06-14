<?php
/**
 * Media Entity Listener
 * author: Haiping Lu
 */
namespace AppBundle\Doctrine\Listener;

use AppBundle\Entity AS Entity;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Response;

class MediaEntityListener
{
    protected $_container;
    
    public function __construct(ConstainerInterface $container)
    {
        $this->_container = $container;
    }
    
    public function postRemove(LifecycleEventArgs $args)
    {

    }
    
    public function preFlush(PreFlushEventArgs $args)
    {

    }
    
    public function prePersist(LifecycleEventArgs $args)
    {
    
        var_dump(gettype($this->_container));die;
    }
    
    public function onFlush(OnFlushEventArgs $args)
    {
        
    }
    
    public function postFlush(PostFlushEventArgs $event)
    {
        
    }
}
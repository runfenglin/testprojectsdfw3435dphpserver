<?php
/**
 * Entity Listener
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

class EntityListener
{
    private $_container;
    
    private $_images;
    
    public function __construct(ContainerInterface $container)
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
    
        $entity = $args->getEntity();
        
        if (method_exists($entity, 'getCreated') 
            && !$entity->getCreated() 
            && method_exists($entity, 'setCreated')) {
            // setting created time
            $entity->setCreated(new \DateTime());
        }
        else if (method_exists($entity, 'setUpdated')) {
            $entity->setUpdated(new \DateTime());
        }
     
    }
    
    public function onFlush(OnFlushEventArgs $args)
    {
        
    }
    
    public function postFlush(PostFlushEventArgs $event)
    {
        
    }
    
}
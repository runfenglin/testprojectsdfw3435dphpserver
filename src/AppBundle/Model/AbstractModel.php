<?php
/**
 * Abstract Model
 * author: Haiping Lu
 */
namespace AppBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\DependencyInjection\Container;
use AppBundle\Exception\InvalidFormException;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\Collection;
use AppBundle\Entity as Entity;

abstract class AbstractModel
{
    protected $_container;
    
    protected $_entity;
    
    public function __construct(Container $container)
    {
        $this->_container = $container;
    }
    
    public function setEntity($entity)
    {
        if ($entity instanceof $this->_entity) {
            $this->_entity = $entity;
        }
        else {
            throw new \Exception('Invalid entity type, ' . gettype($entity) . ' is given, but ' . get_class($this->_entity) . ' is required');
        }
        
        return $this;
    }
    
    public function getEntity()
    {
        return $this->_entity;
    }
    
    public function exposeOne($activity = NULL)
    {
        $expose = $this->expose($activity);
        
        if (is_array($expose)) {
            $expose = array_shift($expose);
        }
        return $expose;
    }
    
    abstract public function expose($entity = NULL);
}
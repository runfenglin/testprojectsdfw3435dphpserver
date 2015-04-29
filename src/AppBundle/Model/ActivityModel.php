<?php

namespace AppBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\DependencyInjection\Container;
use AppBundle\Exception\InvalidFormException;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\Collection;
use AppBundle\Entity\Activity;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Checkin;

class ActivityModel
{
    protected $_container;
    
    protected $_entity;

    public function __construct(Container $container)
    {
        $this->_container = $container;
        
        $this->_entity = new Activity();
    }
    
    public function setEntity(Activity $activity)
    {
        $this->_entity = $activity;
        
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
    
    public function expose($activity = NULL)
    {
        $expose = array();
    
        if (NULL == $activity) {
            $activity = $this->_entity;
        } 
        
        if($activity instanceof Activity && $activity->getId()) {
            $activity = array($activity);
        }
        else if ($activity instanceof Collection) {
            $activity = $activity->toArray();
        }
        else if(!is_array($activity)) {
            return $expose;
        }
        
        foreach($activity as $k => $act) {
            $expose[$k]['id'] = $act->getId(); 
            $expose[$k]['parent_id'] = $act->getParent() ? $act->getParent()->getId() : NULL;
            $expose[$k]['user'] = array(
                'username' => $act->getUser()->getUsername(),
                'name' => $act->getUser()->getName()
            );
            if ($act instanceof Checkin) {
                $expose[$k]['type'] = Activity::CHECKIN;
                $expose[$k]['checkin_reference'] = $act->getCheckinReference();
                $expose[$k]['checkin_name'] = $act->getCheckinName();
                $expose[$k]['comment'] = $act->getComment();
            }
            elseif ($act instanceof Comment){
                $expose[$k]['type'] = Activity::COMMENT;
                $expose[$k]['comment'] = $act->getComment();
            }

            $expose[$k]['children'] = $this->expose($act->getChildren());
            $expose[$k]['created'] = $act->getCreated()->getTimestamp();
        }
        return $expose;
    }
    
    public function postCheckin(array $parameters, $method = Request::METHOD_POST)
    {
        if (!$this->_entity instanceof Checkin) {
            $this->setEntity(new Checkin);
        }
        return $this->_processForm($this->_entity, $parameters, $method);
    }

    public function postComment(array $parameters, $method = Request::METHOD_POST)
    {
        if (!$this->_entity instanceof Comment) {
            $this->setEntity(new Comment);
        }

        return $this->_processForm($this->_entity, $parameters, $method);
    }
    
    private function _processForm(Activity $activity, array $parameters, $method = "PUT")
    {

        $form = $this->_container
                     ->get('form.factory')
                     ->create(
                        $this->_container->get('app.activity.form.type'), 
                        $activity, 
                        array('method' => $method)
                    );
        $form->submit($parameters, Request::METHOD_PUT !== $method);
        if ($form->isValid()) {

            $activity = $form->getData();
            $em = $this->_container->get('doctrine')->getManager();
            
            $em->persist($activity);
            $em->flush();
            
            return $activity;
        }
        else{
            throw new InvalidFormException('Form validation failed', $form);
        }

        return FALSE;
    }
}
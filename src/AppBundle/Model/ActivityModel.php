<?php

namespace AppBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\DependencyInjection\Container;
use AppBundle\Exception\InvalidFormException;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\Collection;

use AppBundle\Entity AS Entity;
//use AppBundle\Entity\Activity;
//use AppBundle\Entity\Comment;
//use AppBundle\Entity\Checkin;

class ActivityModel extends AbstractModel
{
    public function __construct(Container $container)
    {   
        $this->_entity = new Entity\Activity();
        
        $this->_user = $container->get('security.context')->getToken()->getUser();
        
        parent::__construct($container);
    }
    
    public function expose($activity = NULL)
    {
        $expose = array();
   
        if (NULL == $activity) {
            $activity = $this->_entity;
        } 
        
        if($activity instanceof Entity\Activity && $activity->getId()) {
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
                'id' => $act->getUser()->getId(),
                'username' => $act->getUser()->getUsername(),
                'name' => $act->getUser()->getName()
            );
            if ($act instanceof Entity\Checkin) {
                $expose[$k]['user']['avatar'] = $act->getUser()->base64EncodedAvatar();
                $expose[$k]['type'] = Entity\Activity::CHECKIN;
                $expose[$k]['checkin_reference'] = $act->getCheckinReference();
                $expose[$k]['checkin_name'] = $act->getCheckinName();
                $expose[$k]['comment'] = $act->getComment();
                $expose[$k]['like_count'] = $act->getLikeByUsers()->count();
                $expose[$k]['like'] = $act->doILike($this->_user);
            }
            elseif ($act instanceof Entity\Comment){
                $expose[$k]['type'] = Entity\Activity::COMMENT;
                $expose[$k]['comment'] = $act->getComment();
                if($act->getToUser()) {
                    $expose[$k]['to_user'] = array(
                        'id' => $act->getToUser()->getId(),
                        'username' => $act->getToUser()->getUsername(),
                        'name' => $act->getToUser()->getName()
                    );
                }
            }

            $expose[$k]['children'] = $this->expose($act->getChildren());
            $expose[$k]['created'] = $act->getCreated()->getTimestamp();
        }
        return $expose;
    }
    
    public function postCheckin(array $parameters, $method = Request::METHOD_POST)
    {
        if (!$this->_entity instanceof Entity\Checkin) {
            $this->setEntity(new Entity\Checkin);
        }
        return $this->_processForm($this->_entity, $parameters, $method);
    }

    public function postComment(array $parameters, $method = Request::METHOD_POST)
    {
        if (!$this->_entity instanceof Entity\Comment) {
            $this->setEntity(new Entity\Comment);
        }

        return $this->_processForm($this->_entity, $parameters, $method);
    }
    
    private function _processForm(Entity\Activity $activity, array $parameters, $method = "PUT")
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
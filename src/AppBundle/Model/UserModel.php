<?php

namespace AppBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\DependencyInjection\Container;
use AppBundle\Exception\InvalidFormException;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\User;

class UserModel
{
    protected $_container;
    
    protected $_entity;
    
    public function __construct(Container $container)
    {
        $this->_container = $container;
        
        $this->_entity = new User();
    }
    
    public function setEntity(User $user)
    {
        $this->_entity = $user;
        
        return $this;
    }
    
    public function getEntity()
    {
        return $this->_entity;
    }
	
	public function getLatestUpdate()
	{
		$updated = array();
		// For latest comments
		$updated['comment'] = $this->_getUpdatedComment($this->getEntity());
		
		return $updated;
	}
	
	protected function _getUpdatedComment(User $user)
	{
		$items = array();
		
		$em = $this->_container->get('doctrine')->getManager();
        $comments = $em->getRepository('AppBundle:Comment')
		              ->getUpdatedComment($user);
		
		if (count($comments)) {
			// changing last updating time
			$user->setUpdateAt(new \DateTime());
			$em->persist($user);
			$em->flush();
			
			foreach($comments as $k => $c) {
				$items[$k]['id'] = $c->getId();
				$items[$k]['parent'] = $c->getParent()->getId();
				$items[$k]['to_user'] = $c->getToUser() ? $c->getToUser()->getId() : NULL;
				$items[$k]['comment'] = $c->getComment();
			}
		}
		
		return $items;
	}
    
    public function post(array $parameters, $method = Request::METHOD_POST)
    {
        return $this->_processForm($this->_entity, $parameters, $method);
    }

    private function _processForm(User $user, array $parameters, $method = "PUT")
    {
        $form = $this->_container
                     ->get('form.factory')
                     ->create(
                        $this->_container->get('app.user.form.type'), 
                        $user, 
                        array('method' => $method)
                    );
        $form->submit($parameters, Request::METHOD_PUT !== $method);
        if ($form->isValid()) {

            $user = $form->getData();
            $em = $this->_container->get('doctrine')->getManager();
            
            $user->updateToken();
            $em->persist($user);
            $em->flush();
            
            return $user;
        }
        else{
            throw new InvalidFormException('Form validation failed', $form);
        }

        return FALSE;
    }
}   
    
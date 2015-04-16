<?php

namespace AppBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\DependencyInjection\Container;
use AppBundle\Exception\InvalidFormException;
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
	
	public function post(array $parameters)
	{
		return $this->_processForm($this->_entity, $parameters, 'POST');
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
        $form->submit($parameters, 'PATCH' !== $method);
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
	
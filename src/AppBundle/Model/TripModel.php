<?php
namespace AppBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\DependencyInjection\Container;
use AppBundle\Exception\InvalidFormException;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\Collection;
use AppBundle\Entity as Entity;

class TripModel extends AbstractModel
{	
	public function __construct(Container $container)
	{	
		$this->_entity = new Entity\Trip();
		parent::__construct($container);
	}
	
	public function expose($trip = NULL) 
	{
		$expose = array();
		
		return $expose;
	}
	
	public function post(array $parameters, $method = Request::METHOD_POST)
    {
        return $this->_processForm($this->_entity, $parameters, $method);
    }
	
	private function _processForm(Entity\Trip $trip, array $parameters, $method = "PUT")
    {

        $form = $this->_container
                     ->get('form.factory')
                     ->create(
                        $this->_container->get('app.trip.form.type'), 
                        $trip, 
                        array('method' => $method)
                    );
					
        $form->submit($parameters, Request::METHOD_PUT !== $method);
        if ($form->isValid()) {

            $trip = $form->getData();
            $em = $this->_container->get('doctrine')->getManager();
            
            $em->persist($trip);
            $em->flush();
            
            return $trip;
        }
        else{
            throw new InvalidFormException('Form validation failed', $form);
        }

        return FALSE;
    }
}
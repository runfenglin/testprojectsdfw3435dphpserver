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
    
    public function expose($trips = NULL) 
    {
        $expose = array();
        
        if (NULL == $trips) {
            $trips = $this->_entity;
        } 
        
        if($trips instanceof Entity\Trip && $trips->getId()) {
            $trips = array($trips);
        }
        else if ($trips instanceof Collection) {
            $trips = $trips->toArray();
        }
        else if(!is_array($trips)) {
            return $expose;
        }
        
        foreach($trips as $k => $t) {
        
            $expose[$k]['id'] = $t->getId();
            $expose[$k]['time'] = $t->getTime()->getTimestamp();
            $expose[$k]['departure'] = $t->getDeparture();
			$expose[$k]['departure_reference'] = $t->getDepartureReference();
            $expose[$k]['destination'] = $t->getDestination();
			$expose[$k]['destination_reference'] = $t->getDestinationReference();
            $expose[$k]['group'] = $t->getGroup();
            if ($t->getParent()) {
                $expose[$k]['parent'] = $t->getParent()->getId();
            }
            $expose[$k]['offer_count'] = $t->getRideOffers()->count();
        }
        
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
<?php
namespace AppBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\DependencyInjection\Container;
use AppBundle\Exception\InvalidFormException;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\Collection;
use AppBundle\Entity as Entity;

class RideOfferModel extends AbstractModel
{   
    public function __construct(Container $container)
    {   
        $this->_entity = new Entity\RideOffer();
        parent::__construct($container);
    }
    
    public function expose($rideOffers = NULL) 
    {
        $expose = array();
        
        if (NULL == $rideOffers) {
            $rideOffers = $this->_entity;
        } 
        
        if($rideOffers instanceof Entity\RideOffer && $rideOffers->getId()) {
            $rideOffers = array($rideOffers);
        }
        else if ($rideOffers instanceof Collection) {
            $rideOffers = $rideOffers->toArray();
        }
        else if(!is_array($rideOffers)) {
            return $expose;
        }
        
        foreach($rideOffers as $k => $t) {
        
            $expose[$k]['id'] = $t->getId();
			$expose[$k]['user'] = array(
				'id' => $t->getUser()->getId(),
				'name' => $t->getUser()->getName(),
				'avatar' => $t->getUser()->getAvatar()
			);
            $expose[$k]['time'] = $t->getTimestamp();
            $expose[$k]['departure'] = $t->getDeparture();
            $expose[$k]['departure_reference'] = $t->getDepartureReference();
            $expose[$k]['destination'] = $t->getDestination();
            $expose[$k]['destination_reference'] = $t->getDestinationReference();
            $expose[$k]['trip'] = $t->getTrip()->getId();
        }
        
        return $expose;
    }
    
    public function post(array $parameters, $method = Request::METHOD_POST)
    {
        return $this->_processForm($this->_entity, $parameters, $method);
    }
    
    private function _processForm(Entity\RideOffer $rideOffer, array $parameters, $method = "PUT")
    {

        $form = $this->_container
                     ->get('form.factory')
                     ->create(
                        $this->_container->get('app.ride.offer.form.type'), 
                        $rideOffer, 
                        array('method' => $method)
                    );
                    
        $form->submit($parameters, Request::METHOD_PUT !== $method);
        if ($form->isValid()) {

            $rideOffer = $form->getData();
            $em = $this->_container->get('doctrine')->getManager();
            
            $em->persist($rideOffer);
            $em->flush();
            
            return $rideOffer;
        }
        else{
            throw new InvalidFormException('Form validation failed', $form);
        }

        return FALSE;
    }
}
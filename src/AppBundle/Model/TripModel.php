<?php
/**
 * Trip Model
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
            $expose[$k]['user'] = array(
                'id' => $t->getUser()->getId(),
                'name' => $t->getUser()->getName(),
                'avatar' => $t->getUser()->getAvatar()
            );
            
			if ($t->getGroup()){
                foreach($t->getGroupUsers() as $i => $gu){
                    
                    $expose[$k]['group_users'][$i]['name'] = $gu->getUser()->getName();
					$expose[$k]['group_users'][$i]['avatar'] = $gu->getUser()->getAvatar();
                    $expose[$k]['group_users'][$i]['id'] = $gu->getUser()->getId();
                    $expose[$k]['group_users'][$i]['role'] = $gu->getRole();
                }
            }
			elseif ($t->getDriver()){
                $expose[$k]['driver'] = array(
                    'id' => $t->getDriver()->getId(),
                    'name' => $t->getDriver()->getName(),
                    'avatar' => $t->getDriver()->getAvatar()
                );
            }
			else {
				$expose[$k]['offer_count'] = $t->getRideOffers()->count();
			}
            $expose[$k]['time'] = $t->getTime()->getTimestamp();
            $expose[$k]['departure'] = $t->getDeparture();
            $expose[$k]['departure_reference'] = $t->getDepartureReference();
            $expose[$k]['destination'] = $t->getDestination();
            $expose[$k]['destination_reference'] = $t->getDestinationReference();
            $expose[$k]['group'] = $t->getGroup();
            
            if ($t->getParent()) {
                $expose[$k]['parent'] = $t->getParent()->getId();
            }
            
        }
        
        return $expose;
    }
    
    public function post(array $parameters, $method = Request::METHOD_POST)
    {
        return $this->_processForm($this->getEntity(), $parameters, $method);
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
    
	public function filterMyOffer(array $friendRequests)
	{
		$user = $this->_container->get('security.context')->getToken()->getUser();
		
		$filtered = array();
		
		foreach($friendRequests as $r) {
			if (!$r->getRideOffers()->exists(function($k, $e) use ($user) {
				return $e->getUserId() == $user->getId();
			})) {
				$filtered[] = $r;
			}
		}
		
		return $filtered;
	}
    public function pickOffer(Entity\RideOffer $rideOffer)
    {
        $trip = $this->getEntity();
        $mergeElements = array(
            'Departure',
            'DepartureReference',
            'Destination',
            'DestinationReference',
            'Time',
            'Comment'
        );
        
        foreach($mergeElements as $key => $el) {
            
            if ($value = $rideOffer->{'get' . $el}()) {
                $trip->{'set' . $el}($value);
            }
        }

        $trip->setDriver($rideOffer->getUser());
        $trip->getRideOffers()->clear(); 
        
        $em = $this->_container->get('doctrine')->getManager();
        $em->persist($trip);
        $em->flush();
    
        return $this;
    }
    
    public function pushPickNotification(Entity\Trip $trip = NULL)
    {
        if (!$trip){
            $trip = $this->getEntity();
        }
        
        if ($trip->getUser() instanceof Entity\User
            && $trip->getDriver() instanceof Entity\User) {
        
            $em = $this->_container->get('doctrine')->getManager();
            
            $pushMessage = $this->_container
                                ->get('translator')
                                ->trans(
                                    'trip.pick.push.notification', 
                                    array(
                                        '%requester%' => $trip->getUser()
                                                            ->getName()
                                    )
                                );
            $this->_container
                         ->get('push.service')
                         ->push(array($trip->getDriver()), $pushMessage);
        }
        
        return $this;
    }
    
    public function pushCreateNotification(Entity\Trip $trip = NULL)
    {
        if (!$trip){
            $trip = $this->getEntity();
        }
        $requester = $trip->getUser();
        
        if ($requester instanceof Entity\User) {

            $em = $this->_container->get('doctrine')->getManager();
            
            switch($trip->getVisibility()) {
                case Entity\Trip::CIRCLE_FRIEND: {
                    $friends = $em->getRepository('AppBundle:User')
                                  ->getUserFriends($requester);
                    break;
                }
                case Entity\Trip::CIRCLE_FRIEND_OF_FRIEND: {
                    $friends = $em->getRepository('AppBundle:User')
                                  ->getUserFriendsOfFriends($requester);
                    break;
                }
                case Entity\Trip::CIRCLE_GROUP: {
                    $friends = array();
                    
                    foreach($trip->getParent()->getGroupUsers() as $gu) {
                        if($gu->getUser()->isEqualTo($requester)) {
                            continue;
                        }
                        $friends[] = $gu->getUser();
                    }
                    break;
                }
                default: {
                    throw new \RuntimeException('Invalid trip visibility');
                }
            }
         
            if ($trip->getGroup()) {
            
                $pushMessage = $this->_container
                                ->get('translator')
                                ->trans(
                                    'trip.group.create.push.notification', 
                                    array(
                                        '%creator%' => $requester->getName(),
                                        '%departure%' => $trip->getDeparture(),
                                        '%destination%' => $trip->getDestination()
                                    )
                                );
            }
            else {
            
                $pushMessage = $this->_container
                                ->get('translator')
                                ->trans(
                                    'trip.create.push.notification', 
                                    array(
                                        '%requester%' => $requester->getName(),
                                        '%departure%' => $trip->getDeparture(),
                                        '%destination%' => $trip->getDestination()
                                    )
                                );
            }
            
            $this->_container
                         ->get('push.service')
                         ->push($friends, $pushMessage);
            
        }
        
        return $this;
    }
}
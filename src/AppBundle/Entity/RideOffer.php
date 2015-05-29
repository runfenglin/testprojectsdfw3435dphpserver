<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints;

/**
 * RideOffer
 *
 * @ORM\Table(name="tr_ride_offer")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\RideOfferRepository")
 * @Constraints\UniqueEntity(fields={"trip", "user"}, message="@rideOffer.accept.duplicate")
 */
class RideOffer
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="departure", type="string", length=128, nullable=TRUE)
     */
    private $departure;

	    /**
     * @var string
     *
     * @ORM\Column(name="departure_reference", type="string", length=255, nullable=TRUE)
     */
    private $departureReference;
	
    /**
     * @var string
     *
     * @ORM\Column(name="destination", type="string", length=128, nullable=TRUE)
     */
    private $destination;

    /**
     * @var string
     *
     * @ORM\Column(name="destination_reference", type="string", length=255, nullable=TRUE)
     */
    private $destinationReference;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime", nullable=TRUE)
     */
    private $time;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=TRUE)
     */
    private $comment;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="trip_id", type="integer")
     */
    private $tripId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="Trip", inversedBy="rideOffers")
     * @ORM\JoinColumn(name="trip_id", referencedColumnName="id")
     **/    
    private $trip;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="rideOffers")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/    
    private $user;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return RideOffer
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set departure
     *
     * @param string $departure
     * @return RideOffer
     */
    public function setDeparture($departure)
    {
        $this->departure = $departure;

        return $this;
    }

    /**
     * Get departure
     *
     * @return string 
     */
    public function getDeparture()
    {
        return $this->departure;
    }

    /**
     * Set destination
     *
     * @param string $destination
     * @return RideOffer
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Get destination
     *
     * @return string 
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     * @return RideOffer
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return RideOffer
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set tripId
     *
     * @param integer $tripId
     * @return RideOffer
     */
    public function setTripId($tripId)
    {
        $this->tripId = $tripId;

        return $this;
    }

    /**
     * Get tripId
     *
     * @return integer 
     */
    public function getTripId()
    {
        return $this->tripId;
    }

    /**
     * Set trip
     *
     * @param \AppBundle\Entity\Trip $trip
     * @return RideOffer
     */
    public function setTrip(\AppBundle\Entity\Trip $trip = null)
    {
        $this->trip = $trip;

        return $this;
    }

    /**
     * Get trip
     *
     * @return \AppBundle\Entity\Trip 
     */
    public function getTrip()
    {
        return $this->trip;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return RideOffer
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return RideOffer
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }
    
    public function getTimestamp()
    {
        if ($this->getTime()) {
            return $this->getTime()->getTimestamp();
        }
        return NULL;
    }

    /**
     * Set departureReference
     *
     * @param string $departureReference
     * @return RideOffer
     */
    public function setDepartureReference($departureReference)
    {
        $this->departureReference = $departureReference;

        return $this;
    }

    /**
     * Get departureReference
     *
     * @return string 
     */
    public function getDepartureReference()
    {
        return $this->departureReference;
    }

    /**
     * Set destinationReference
     *
     * @param string $destinationReference
     * @return RideOffer
     */
    public function setDestinationReference($destinationReference)
    {
        $this->destinationReference = $destinationReference;

        return $this;
    }

    /**
     * Get destinationReference
     *
     * @return string 
     */
    public function getDestinationReference()
    {
        return $this->destinationReference;
    }
}

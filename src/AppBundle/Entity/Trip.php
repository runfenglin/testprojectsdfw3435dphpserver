<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trip
 *
 * @ORM\Table(name="tr_user")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TripRepository")
 */
class Trip
{
	const CIRCLE_FRIEND = 1;
	const CIRCLE_FRIEND_OF_FRIEND = 2;
	const CIRCLE_PUBLIC = 4;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="departure", type="string", length=128)
     */
    private $departure;

    /**
     * @var string
     *
     * @ORM\Column(name="destination", type="string", length=128)
     */
    private $destination;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;

    /**
     * @var integer
     *
     * @ORM\Column(name="return_id", type="integer", nullable=TRUE)
     */
    private $returnId;

    /**
     * @var integer
     *
     * @ORM\Column(name="group_id", type="integer", nullable=TRUE)
     */
    private $groupId;

    /**
     * @var integer
     *
     * @ORM\Column(name="visibility", type="integer")
     */
    private $visibility = self::CIRCLE_FRIEND;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="driver_id", type="integer")
     */
    private $driverId;
	
    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255)
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\OneToOne(targetEntity="Trip")
     * @ORM\JoinColumn(name="return_id", referencedColumnName="id")
     **/
	private $returnTrip;
	
	/**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="requests")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
	private $user;
	
	/**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="offers")
     * @ORM\JoinColumn(name="driver_id", referencedColumnName="id")
     **/	
	private $driver;
	
	/**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="acceptedRequests")
     **/
	private $rideOffers;

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
     * Set departure
     *
     * @param string $departure
     * @return Trip
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
     * @return Trip
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
     * @return Trip
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
     * Set returnId
     *
     * @param integer $returnId
     * @return Trip
     */
    public function setReturnId($returnId)
    {
        $this->returnId = $returnId;

        return $this;
    }

    /**
     * Get returnId
     *
     * @return integer 
     */
    public function getReturnId()
    {
        return $this->returnId;
    }

    /**
     * Set groupId
     *
     * @param integer $groupId
     * @return Trip
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId
     *
     * @return integer 
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set visibility
     *
     * @param integer $visibility
     * @return Trip
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * Get visibility
     *
     * @return integer 
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return Trip
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
     * Set driverId
     *
     * @param integer $driverId
     * @return Trip
     */
    public function setDriverId($driverId)
    {
        $this->driverId = $driverId;

        return $this;
    }

    /**
     * Get driverId
     *
     * @return integer 
     */
    public function getDriverId()
    {
        return $this->driverId;
    }
	
    /**
     * Set comment
     *
     * @param string $comment
     * @return Trip
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

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Trip
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
     * Constructor
     */
    public function __construct()
    {
        $this->rideOffers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set returnTrip
     *
     * @param \AppBundle\Entity\Trip $returnTrip
     * @return Trip
     */
    public function setReturnTrip(\AppBundle\Entity\Trip $returnTrip = null)
    {
        $this->returnTrip = $returnTrip;

        return $this;
    }

    /**
     * Get returnTrip
     *
     * @return \AppBundle\Entity\Trip 
     */
    public function getReturnTrip()
    {
        return $this->returnTrip;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Trip
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
     * Set driver
     *
     * @param \AppBundle\Entity\User $driver
     * @return Trip
     */
    public function setDriver(\AppBundle\Entity\User $driver = null)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * Get driver
     *
     * @return \AppBundle\Entity\User 
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Add rideOffers
     *
     * @param \AppBundle\Entity\User $rideOffers
     * @return Trip
     */
    public function addRideOffer(\AppBundle\Entity\User $rideOffers)
    {
        $this->rideOffers[] = $rideOffers;

        return $this;
    }

    /**
     * Remove rideOffers
     *
     * @param \AppBundle\Entity\User $rideOffers
     */
    public function removeRideOffer(\AppBundle\Entity\User $rideOffers)
    {
        $this->rideOffers->removeElement($rideOffers);
    }

    /**
     * Get rideOffers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRideOffers()
    {
        return $this->rideOffers;
    }
}

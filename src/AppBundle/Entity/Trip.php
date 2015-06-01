<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trip
 *
 * @ORM\Table(name="tr_trip")
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
     * @ORM\Column(name="departure_reference", type="string", length=255)
     */
    private $departureReference;
    
    /**
     * @var string
     *
     * @ORM\Column(name="destination", type="string", length=128)
     */
    private $destination;

    /**
     * @var string
     *
     * @ORM\Column(name="destination_reference", type="string", length=255)
     */
    private $destinationReference;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;

    /**
     * @var integer
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=TRUE)
     */
    private $parentId;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_group", type="boolean")
     */
    private $group = FALSE;

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
     * @ORM\Column(name="driver_id", type="integer", nullable=TRUE)
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
     * @ORM\OneToMany(targetEntity="Trip", mappedBy="parent", cascade={"persist", "remove"}, orphanRemoval=true)
     **/
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Trip", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     **/
    private $parent;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="trips")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="offers")
     * @ORM\JoinColumn(name="driver_id", referencedColumnName="id")
     **/    
    private $driver;
    
    /**
     * @ORM\OneToMany(targetEntity="RideOffer", mappedBy="trip", cascade={"persist", "remove"}, orphanRemoval=true)
     **/
    private $rideOffers;

    /**
     * @ORM\OneToMany(targetEntity="GroupUser", mappedBy="trip")
     **/
    private $groupUsers;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->rideOffers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groupUsers = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set parentId
     *
     * @param integer $parentId
     * @return Trip
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return integer 
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set group
     *
     * @param boolean $group
     * @return Trip
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return boolean 
     */
    public function getGroup()
    {
        return $this->group;
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
     * Add children
     *
     * @param \AppBundle\Entity\Trip $children
     * @return Trip
     */
    public function addChild(\AppBundle\Entity\Trip $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \AppBundle\Entity\Trip $children
     */
    public function removeChild(\AppBundle\Entity\Trip $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \AppBundle\Entity\Trip $parent
     * @return Trip
     */
    public function setParent(\AppBundle\Entity\Trip $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\Trip 
     */
    public function getParent()
    {
        return $this->parent;
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
     * @param \AppBundle\Entity\RideOffer $rideOffers
     * @return Trip
     */
    public function addRideOffer(\AppBundle\Entity\RideOffer $rideOffers)
    {
        $this->rideOffers[] = $rideOffers;

        return $this;
    }

    /**
     * Remove rideOffers
     *
     * @param \AppBundle\Entity\RideOffer $rideOffers
     */
    public function removeRideOffer(\AppBundle\Entity\RideOffer $rideOffers)
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

    /**
     * Add groupUsers
     *
     * @param \AppBundle\Entity\GroupUser $groupUsers
     * @return Trip
     */
    public function addGroupUser(\AppBundle\Entity\GroupUser $groupUsers)
    {
        $this->groupUsers[] = $groupUsers;

        return $this;
    }

    /**
     * Remove groupUsers
     *
     * @param \AppBundle\Entity\GroupUser $groupUsers
     */
    public function removeGroupUser(\AppBundle\Entity\GroupUser $groupUsers)
    {
        $this->groupUsers->removeElement($groupUsers);
    }

    /**
     * Get groupUsers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroupUsers()
    {
        return $this->groupUsers;
    }

    /**
     * Set departureReference
     *
     * @param string $departureReference
     * @return Trip
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
     * @return Trip
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

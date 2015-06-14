<?php
/**
 * GroupUser Entity
 * author: Haiping Lu
 */
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GroupUser
 *
 * @ORM\Table(name="tr_group_user")
 * @ORM\Entity
 */
class GroupUser
{
    const ROLE_MEMBER = 0;
    const ROLE_CREATOR = 1;
    const ROLE_DRIVER = 2;
    const ROLE_REQUESTOR = 4;
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
     * @var integer
     *
     * @ORM\Column(name="trip_id", type="integer")
     */
    private $tripId;

    /**
     * @var integer
     *
     * @ORM\Column(name="role", type="integer")
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="groupTrips")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/    
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Trip", inversedBy="groupUsers")
     * @ORM\JoinColumn(name="trip_id", referencedColumnName="id")
     **/    
    private $trip;

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
     * @return GroupUser
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
     * Set tripId
     *
     * @param integer $tripId
     * @return GroupUser
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
     * Set role
     *
     * @param integer $role
     * @return GroupUser
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return integer 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return GroupUser
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
     * Set trip
     *
     * @param \AppBundle\Entity\Trip $trip
     * @return GroupUser
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
}

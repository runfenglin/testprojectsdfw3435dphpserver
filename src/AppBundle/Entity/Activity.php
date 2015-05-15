<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Activity
 *
 * @ORM\Table(name="tr_activity")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"checkin"="Checkin","comment"="Comment"})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ActivityRepository")
 */
class Activity
{
    const CHECKIN = 'checkin';
    const COMMENT = 'commment';
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    protected $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=TRUE)
     */
    protected $parentId;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="activities")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    protected $user;
    
    /**
     * @ORM\ManyToMany(targetEntity="Media", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinTable(name="tr_activity_media",
     *      joinColumns={@ORM\JoinColumn(name="activity_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="media_id", referencedColumnName="id", unique=true)}
     *      )
     **/
    protected $medias;
    
    /**
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="parent", cascade={"persist", "remove"}, orphanRemoval=true)
     **/
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Activity", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     **/
    private $parent;
    
    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="likes")
     */
    private $likeByUsers;
    
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
     * @return Activity
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
     * Set parentId
     *
     * @param integer $parentId
     * @return Activity
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
     * Set created
     *
     * @param \DateTime $created
     * @return Activity
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
        $this->medias = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->likeByUsers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Activity
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
     * Add medias
     *
     * @param \AppBundle\Entity\Media $medias
     * @return Activity
     */
    public function addMedia(\AppBundle\Entity\Media $medias)
    {
        $this->medias[] = $medias;

        return $this;
    }

    /**
     * Remove medias
     *
     * @param \AppBundle\Entity\Media $medias
     */
    public function removeMedia(\AppBundle\Entity\Media $medias)
    {
        $this->medias->removeElement($medias);
    }

    /**
     * Get medias
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * Add children
     *
     * @param \AppBundle\Entity\Activity $children
     * @return Activity
     */
    public function addChild(\AppBundle\Entity\Activity $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \AppBundle\Entity\Activity $children
     */
    public function removeChild(\AppBundle\Entity\Activity $children)
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
     * @param \AppBundle\Entity\Activity $parent
     * @return Activity
     */
    public function setParent(\AppBundle\Entity\Activity $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\Activity 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add likeByUsers
     *
     * @param \AppBundle\Entity\User $likeByUsers
     * @return Activity
     */
    public function addLikeByUser(\AppBundle\Entity\User $likeByUsers)
    {
        $this->likeByUsers[] = $likeByUsers;

        return $this;
    }

    /**
     * Remove likeByUsers
     *
     * @param \AppBundle\Entity\User $likeByUsers
     */
    public function removeLikeByUser(\AppBundle\Entity\User $likeByUsers)
    {
        $this->likeByUsers->removeElement($likeByUsers);
    }

    /**
     * Get likeByUsers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLikeByUsers()
    {
        return $this->likeByUsers;
    }
}

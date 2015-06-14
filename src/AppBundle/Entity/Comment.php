<?php
/**
 * Comment Entity
 * author: Haiping Lu
 */
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Comment
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CommentRepository")
 */
class Comment extends Activity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="to_user_id", type="integer", nullable=TRUE)
     */
    protected $toUserId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=TRUE)
     */
    protected $comment;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="to_user_id", referencedColumnName="id")
     **/
    protected $toUser;
    
    /**
     * Set comment
     *
     * @param string $comment
     * @return Comment
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
     * Set toUserId
     *
     * @param integer $toUserId
     * @return Activity
     */
    public function setToUserId($toUserId)
    {
        $this->toUserId = $toUserId;

        return $this;
    }

    /**
     * Get toUserId
     *
     * @return integer 
     */
    public function getToUserId()
    {
        return $this->toUserId;
    }
    
    /**
     * Set toUser
     *
     * @param \AppBundle\Entity\User $toUser
     * @return Activity
     */
    public function setToUser(\AppBundle\Entity\User $toUser = null)
    {
        $this->toUser = $toUser;

        return $this;
    }

    /**
     * Get toUser
     *
     * @return \AppBundle\Entity\User 
     */
    public function getToUser()
    {
        return $this->toUser;
    }
}
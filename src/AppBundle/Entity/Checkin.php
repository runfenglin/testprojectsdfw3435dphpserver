<?php
/**
 * Checkin Entity
 * author: Haiping Lu
 */
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Checkin
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CheckinRepository")
 */
class Checkin extends Activity
{
    /**
     * @var string
     *
     * @ORM\Column(name="checkin_reference", type="string", length=255, nullable=TRUE)
     */
    protected $checkinReference;

    /**
     * @var string
     *
     * @ORM\Column(name="checkin_name", type="string", length=128, nullable=TRUE)
     */
    protected $checkinName;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=TRUE)
     */
    protected $comment;
    
    /**
     * Set checkinReference
     *
     * @param string $checkinReference
     * @return Checkin
     */
    public function setCheckinReference($checkinReference)
    {
        $this->checkinReference = $checkinReference;

        return $this;
    }

    /**
     * Get checkinReference
     *
     * @return string 
     */
    public function getCheckinReference()
    {
        return $this->checkinReference;
    }

    /**
     * Set checkinName
     *
     * @param string $checkinName
     * @return Checkin
     */
    public function setCheckinName($checkinName)
    {
        $this->checkinName = $checkinName;

        return $this;
    }

    /**
     * Get checkinName
     *
     * @return string 
     */
    public function getCheckinName()
    {
        return $this->checkinName;
    }

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
}

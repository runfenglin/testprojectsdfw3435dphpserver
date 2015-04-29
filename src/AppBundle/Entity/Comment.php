<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Comment
 *
 * @ORM\Entity
 */
class Comment extends Activity
{
    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=TRUE)
     */
    protected $comment;
    
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
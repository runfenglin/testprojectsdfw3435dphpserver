<?php
/**
 * Token Entity
 * author: Haiping Lu
 */
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Token
 *
 * @ORM\Table(name="tr_token", uniqueConstraints={ @ORM\UniqueConstraint(name="key_idx", columns={"api_key"})})
 * @ORM\Entity
 */
class Token
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
     * @var string
     *
     * @ORM\Column(name="api_key", type="string", length=32)
     */
    private $key;


    /**
     * @ORM\OneToOne(targetEntity="User", mappedBy="token")
     */
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
     * Set key
     *
     * @param string $key
     * @return Token
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string 
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Token
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
}

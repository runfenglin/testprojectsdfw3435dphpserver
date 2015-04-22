<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SocialLogin
 *
 * @ORM\Table(name="tr_social_login")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SocialLoginRepository")
 */
class SocialLogin
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
     * @var integer
     *
     * @ORM\Column(name="type_id", type="integer")
     */
    private $typeId;

    /**
     * @var string
     *
     * @ORM\Column(name="sm_id", type="string", length=32)
     */
    private $smId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="sm_name", type="string", length=64)
     */
    private $smName;

    /**
     * @var string
     *
     * @ORM\Column(name="sm_token", type="string", length=255)
     */
    private $smToken;

    /**
     * @var string
     *
     * @ORM\Column(name="sm_email", type="string", length=128)
     */
    private $smEmail;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="socialAccounts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    private $user;   
    
    /**
     * @ORM\ManyToOne(targetEntity="SocialType", inversedBy="socialAccounts")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    private $type;

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
     * @return SocialLogin
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
     * Set typeId
     *
     * @param integer $typeId
     * @return SocialLogin
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;

        return $this;
    }

    /**
     * Get typeId
     *
     * @return integer 
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * Set smToken
     *
     * @param string $smToken
     * @return SocialLogin
     */
    public function setSmToken($smToken)
    {
        $this->smToken = $smToken;

        return $this;
    }

    /**
     * Get smToken
     *
     * @return string 
     */
    public function getSmToken()
    {
        return $this->smToken;
    }

    /**
     * Set smEmail
     *
     * @param string $smEmail
     * @return SocialLogin
     */
    public function setSmEmail($smEmail)
    {
        $this->smEmail = $smEmail;

        return $this;
    }

    /**
     * Get smEmail
     *
     * @return string 
     */
    public function getSmEmail()
    {
        return $this->smEmail;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return SocialLogin
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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return SocialLogin
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
     * Set type
     *
     * @param \AppBundle\Entity\SocialType $type
     * @return SocialLogin
     */
    public function setType(\AppBundle\Entity\SocialType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\SocialType 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set smId
     *
     * @param string $smId
     * @return SocialLogin
     */
    public function setSmId($smId)
    {
        $this->smId = $smId;

        return $this;
    }

    /**
     * Get smId
     *
     * @return string 
     */
    public function getSmId()
    {
        return $this->smId;
    }

    /**
     * Set smName
     *
     * @param string $smName
     * @return SocialLogin
     */
    public function setSmName($smName)
    {
        $this->smName = $smName;

        return $this;
    }

    /**
     * Get smName
     *
     * @return string 
     */
    public function getSmName()
    {
        return $this->smName;
    }
}

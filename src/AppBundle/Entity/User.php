<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="tr_user")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 */
class User
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
     * @ORM\Column(name="username", type="string", length=32)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=64)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=64)
     */
    private $salt;	
	
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=128)
     */
    private $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=32)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="avatar_id", type="integer", nullable=TRUE)
     */
    private $avatarId;

    /**
     * @var integer
     *
     * @ORM\Column(name="country_id", type="integer", nullable=TRUE)
     */
    private $countryId;

    /**
     * @var integer
     *
     * @ORM\Column(name="token_id", type="integer", nullable=TRUE)
     */
    private $tokenId;	
	
    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=15, nullable=TRUE)
     */
    private $phone;

    /**
     * @var integer
     *
     * @ORM\Column(name="friend_count", type="integer")
     */
    private $friendCount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="login_at", type="datetime")
     */
    private $loginAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

	
	
	
	/**
	 * @ORM\OneToOne(targetEntity="Token", mappedBy="user")
	 */
	private $token;
	
	
	/**
	 * @ORM\ManyToOne(targetEntity="Country", inversedBy="users")
	 * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
	 */
	private $country;
	
	/**
	 * @ORM\OneToOne(targetEntity="Media")
	 * @ORM\JoinColumn(name="avatar_id", referencedColumnName="id")
	 */
	private $avatar;
	
    /**
     * @ORM\OneToMany(targetEntity="SocialLogin", mappedBy="user")
     **/
	private $socialAccounts;

	
	
	public function getRoles()
	{
		return array('ROLE_USER');
	}
	
	public function eraseCredentials()
	{
		
	}
	
    public function serialize()
    {
        return serialize(array(
            $this->password,
            $this->salt,
            $this->name,
            $this->username,
            $this->enabled,
            $this->id,
        ));
    }
	
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        // add a few extra elements in the array to ensure that we have enough keys when unserializing
        // older data which does not include all properties.
        $data = array_merge($data, array_fill(0, 2, null));

        list(
            $this->password,
            $this->salt,
			$this->name,
            $this->username,
            $this->enabled,
            $this->id
        ) = $data;
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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return User
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set avatarId
     *
     * @param integer $avatarId
     * @return User
     */
    public function setAvatarId($avatarId)
    {
        $this->avatarId = $avatarId;

        return $this;
    }

    /**
     * Get avatarId
     *
     * @return integer 
     */
    public function getAvatarId()
    {
        return $this->avatarId;
    }

    /**
     * Set countryId
     *
     * @param integer $countryId
     * @return User
     */
    public function setCountryId($countryId)
    {
        $this->countryId = $countryId;

        return $this;
    }

    /**
     * Get countryId
     *
     * @return integer 
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set friendCount
     *
     * @param integer $friendCount
     * @return User
     */
    public function setFriendCount($friendCount)
    {
        $this->friendCount = $friendCount;

        return $this;
    }

    /**
     * Get friendCount
     *
     * @return integer 
     */
    public function getFriendCount()
    {
        return $this->friendCount;
    }

    /**
     * Set loginAt
     *
     * @param \DateTime $loginAt
     * @return User
     */
    public function setLoginAt($loginAt)
    {
        $this->loginAt = $loginAt;

        return $this;
    }

    /**
     * Get loginAt
     *
     * @return \DateTime 
     */
    public function getLoginAt()
    {
        return $this->loginAt;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return User
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
        $this->socialAccounts = new \Doctrine\Common\Collections\ArrayCollection();
		$this->enabled = TRUE;
		$this->salt = md5(uniqid(null, true));
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set tokenId
     *
     * @param integer $tokenId
     * @return User
     */
    public function setTokenId($tokenId)
    {
        $this->tokenId = $tokenId;

        return $this;
    }

    /**
     * Get tokenId
     *
     * @return integer 
     */
    public function getTokenId()
    {
        return $this->tokenId;
    }

    /**
     * Set token
     *
     * @param \AppBundle\Entity\Token $token
     * @return User
     */
    public function setToken(\AppBundle\Entity\Token $token = null)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return \AppBundle\Entity\Token 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set country
     *
     * @param \AppBundle\Entity\Country $country
     * @return User
     */
    public function setCountry(\AppBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \AppBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set avatar
     *
     * @param \AppBundle\Entity\Media $avatar
     * @return User
     */
    public function setAvatar(\AppBundle\Entity\Media $avatar = null)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return \AppBundle\Entity\Media 
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Add socialAccounts
     *
     * @param \AppBundle\Entity\SocialLogin $socialAccounts
     * @return User
     */
    public function addSocialAccount(\AppBundle\Entity\SocialLogin $socialAccounts)
    {
        $this->socialAccounts[] = $socialAccounts;

        return $this;
    }

    /**
     * Remove socialAccounts
     *
     * @param \AppBundle\Entity\SocialLogin $socialAccounts
     */
    public function removeSocialAccount(\AppBundle\Entity\SocialLogin $socialAccounts)
    {
        $this->socialAccounts->removeElement($socialAccounts);
    }

    /**
     * Get socialAccounts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSocialAccounts()
    {
        return $this->socialAccounts;
    }
}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints;
use AppBundle\Entity\Token;
/**
 * User
 *
 * @ORM\Table(name="tr_user", uniqueConstraints={@ORM\UniqueConstraint(name="username_idx", columns={"username"}), @ORM\UniqueConstraint(name="phone_idx", columns={"country", "phone"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 * @Constraints\UniqueEntity(fields={"username"}, message="Username already exist")
 * @Constraints\UniqueEntity(fields={"phone", "country"}, message="Phone already exist")
 */
class User implements AdvancedUserInterface, EquatableInterface, \Serializable
{
    const AVATAR_UPLOAD_PATH = 'user/avatar';
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
     * @ORM\Column(name="username", type="string", length=40)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=128, nullable=TRUE)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=64, nullable=TRUE)
     */
    private $salt;  
    
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=128, nullable=TRUE)
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
     * @ORM\Column(name="country", type="string", length=5, nullable=TRUE)
     */
    private $country;   
    
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
     *
    private $friendCount = 0;
     */
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="login_at", type="datetime")
     */
    private $loginAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_at", type="datetime", nullable=TRUE)
     */
    private $updateAt;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="myFriends")
     */
    private $friendsWithMe;
    
    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="friendsWithMe")
     * @ORM\JoinTable(name="tr_friend",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="friend_user_id", referencedColumnName="id")}
     *      )
     */
    private $myFriends; 
    
    /**
     * @ORM\ManyToMany(targetEntity="Activity", inversedBy="likeByUsers",cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinTable(name="tr_user_like_activity")
     */
    private $likes;
    
    /**
     * @ORM\OneToOne(targetEntity="Token", mappedBy="user", cascade={"persist", "remove"})
     */
    private $token;
    
    /**
     * @ORM\OneToOne(targetEntity="Media", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="avatar_id", referencedColumnName="id")
     */
    private $avatar;
    
    /**
     * @ORM\OneToMany(targetEntity="SocialLogin", mappedBy="user", cascade={"persist", "remove"})
     **/
    private $socialAccounts;

    /**
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="user")
     * @ORM\OrderBy({"created"="DESC"})
     **/
    private $activities;    
    
    /**
     * @ORM\OneToMany(targetEntity="Trip", mappedBy="user")
     **/
    private $trips;
   
    /**
     * @ORM\OneToMany(targetEntity="Trip", mappedBy="driver")
     **/
    private $offers;
    
    /**
     * @ORM\OneToMany(targetEntity="RideOffer", mappedBy="user")
     **/
    private $rideOffers;
    
    /**
     * @ORM\OneToMany(targetEntity="GroupUser", mappedBy="user")
     **/
    private $groupTrips;
    
    public function getRoles()
    {
        return array('ROLE_USER');
    }
    
    public function eraseCredentials()
    {
        
    }
    
    public function isAccountNonExpired()
    {
    
    }
    
    public function isAccountNonLocked()
    {
    
    }
    
    public function isCredentialsNonExpired()
    {
    
    }
    
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof $this) {
            return FALSE;
        }

        if ($this->password !== $user->getPassword()) {
            return FALSE;
        }

        if ($this->salt !== $user->getSalt()) {
            return FALSE;
        }

        if ($this->username !== $user->getUsername()) {
            return FALSE;
        }

        return true;
    }
    
    public function isEnabled()
    {
        return (boolean) $this->enabled;
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
     * Set country
     *
     * @param integer $country
     * @return User
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return integer 
     */
    public function getCountry()
    {
        return $this->country;
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
     *
    public function setFriendCount($friendCount)
    {
        $this->friendCount = $friendCount;

        return $this;
    }
     
    /**
     * Get friendCount
     *
     * @return integer 
     *
    public function getFriendCount()
    {
        return $this->friendCount;
    }
     */
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
        $this->friendsWithMe = new \Doctrine\Common\Collections\ArrayCollection();
        $this->myFriends = new \Doctrine\Common\Collections\ArrayCollection();
        $this->activities = new \Doctrine\Common\Collections\ArrayCollection();
        $this->likes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->trips = new \Doctrine\Common\Collections\ArrayCollection();
        $this->offers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->rideOffers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groupTrips = new \Doctrine\Common\Collections\ArrayCollection();
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
    
    /**
     * Customized method to filter social account by social type
     */
    public function getSocialAccountByType($type)
    {
        return $this->getSocialAccounts()->filter(
            
            function($account) use ($type) {
                return $type == $account->getType()->getCode();
            }
            
        )->first();
    }
    
    /**
     * Customized method to generate api key, it might move to other place later
     */
    public static function generateApiKey()
    {
        return md5(uniqid(mt_rand(), TRUE));
    }

    /**
     * Customized method to generate username for social login, it might move to other place later
     */
    public function generateUsername()
    {
        if (!$this->getUsername()) {
            $this->setUsername(uniqid(mt_rand(), TRUE));
        }
    }
    
    /**
     * Customized method to update token, it might move to other place later
     */
    public function updateToken()
    {
        $apiKey = self::generateApiKey();
            
        if ($this->getToken()) {
            $this->getToken()->setKey($apiKey);
        }
        else {
            $token = new Token();
            $token->setUser($this);
            $token->setKey($apiKey);
            $this->setToken($token);
        }
        
        $this->setLoginAt(new \DateTime());
    }

    /**
     * Add friendsWithMe
     *
     * @param \AppBundle\Entity\User $friendsWithMe
     * @return User
     */
    public function addFriendsWithMe(\AppBundle\Entity\User $friendsWithMe)
    {
        $this->friendsWithMe[] = $friendsWithMe;

        return $this;
    }

    /**
     * Remove friendsWithMe
     *
     * @param \AppBundle\Entity\User $friendsWithMe
     */
    public function removeFriendsWithMe(\AppBundle\Entity\User $friendsWithMe)
    {
        $this->friendsWithMe->removeElement($friendsWithMe);
    }

    /**
     * Get friendsWithMe
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFriendsWithMe()
    {
        return $this->friendsWithMe;
    }

    /**
     * Add myFriends
     *
     * @param \AppBundle\Entity\User $myFriends
     * @return User
     */
    public function addMyFriend(\AppBundle\Entity\User $myFriends)
    {
        $this->myFriends[] = $myFriends;

        return $this;
    }

    /**
     * Remove myFriends
     *
     * @param \AppBundle\Entity\User $myFriends
     */
    public function removeMyFriend(\AppBundle\Entity\User $myFriends)
    {
        $this->myFriends->removeElement($myFriends);
    }

    /**
     * Get myFriends
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMyFriends()
    {
        return $this->myFriends;
    }

    /**
     * Add activities
     *
     * @param \AppBundle\Entity\Activity $activities
     * @return User
     */
    public function addActivity(\AppBundle\Entity\Activity $activities)
    {
        $this->activities[] = $activities;

        return $this;
    }

    /**
     * Remove activities
     *
     * @param \AppBundle\Entity\Activity $activities
     */
    public function removeActivity(\AppBundle\Entity\Activity $activities)
    {
        $this->activities->removeElement($activities);
    }

    /**
     * Get activities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActivities()
    {
        return $this->activities;
    }
/*
    /**
     * Add requests
     *
     * @param \AppBundle\Entity\Trip $requests
     * @return User
     *
    public function addRequest(\AppBundle\Entity\Trip $requests)
    {
        $this->requests[] = $requests;

        return $this;
    }

    /**
     * Remove requests
     *
     * @param \AppBundle\Entity\Trip $requests
     *
    public function removeRequest(\AppBundle\Entity\Trip $requests)
    {
        $this->requests->removeElement($requests);
    }

    /**
     * Get requests
     *
     * @return \Doctrine\Common\Collections\Collection 
     *
    public function getRequests()
    {
        return $this->requests;
    }
*/
    /**
     * Add offers
     *
     * @param \AppBundle\Entity\Trip $offers
     * @return User
     */
    public function addOffer(\AppBundle\Entity\Trip $offers)
    {
        $this->offers[] = $offers;

        return $this;
    }

    /**
     * Remove offers
     *
     * @param \AppBundle\Entity\Trip $offers
     */
    public function removeOffer(\AppBundle\Entity\Trip $offers)
    {
        $this->offers->removeElement($offers);
    }

    /**
     * Get offers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOffers()
    {
        return $this->offers;
    }
/*
    /**
     * Add acceptedRequests
     *
     * @param \AppBundle\Entity\Trip $acceptedRequests
     * @return User
     *
    public function addAcceptedRequest(\AppBundle\Entity\Trip $acceptedRequests)
    {
        $this->acceptedRequests[] = $acceptedRequests;

        return $this;
    }

    /**
     * Remove acceptedRequests
     *
     * @param \AppBundle\Entity\Trip $acceptedRequests
     *
    public function removeAcceptedRequest(\AppBundle\Entity\Trip $acceptedRequests)
    {
        $this->acceptedRequests->removeElement($acceptedRequests);
    }

    /**
     * Get acceptedRequests
     *
     * @return \Doctrine\Common\Collections\Collection 
     *
    public function getAcceptedRequests()
    {
        return $this->acceptedRequests;
    }
*/
    /**
     * Set updateAt
     *
     * @param \DateTime $updateAt
     * @return User
     */
    public function setUpdateAt($updateAt)
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * Get updateAt
     *
     * @return \DateTime 
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }
    
    public function base64EncodedAvatar()
    {
        if($this->getAvatar()) {
            return $this->getAvatar()->base64Encoded();
        }
        
        return NULL;
    }

    /**
     * Add likes
     *
     * @param \AppBundle\Entity\Activity $likes
     * @return User
     */
    public function addLike(\AppBundle\Entity\Activity $likes)
    {
        $this->likes[] = $likes;

        return $this;
    }

    /**
     * Remove likes
     *
     * @param \AppBundle\Entity\Activity $likes
     */
    public function removeLike(\AppBundle\Entity\Activity $likes)
    {
        $this->likes->removeElement($likes);
    }

    /**
     * Get likes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Add trips
     *
     * @param \AppBundle\Entity\Trip $trips
     * @return User
     */
    public function addTrip(\AppBundle\Entity\Trip $trips)
    {
        $this->trips[] = $trips;

        return $this;
    }

    /**
     * Remove trips
     *
     * @param \AppBundle\Entity\Trip $trips
     */
    public function removeTrip(\AppBundle\Entity\Trip $trips)
    {
        $this->trips->removeElement($trips);
    }

    /**
     * Get trips
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTrips()
    {
        return $this->trips;
    }

    /**
     * Add rideOffers
     *
     * @param \AppBundle\Entity\RideOffer $rideOffers
     * @return User
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
     * Add groupTrips
     *
     * @param \AppBundle\Entity\GroupUser $groupTrips
     * @return User
     */
    public function addGroupTrip(\AppBundle\Entity\GroupUser $groupTrips)
    {
        $this->groupTrips[] = $groupTrips;

        return $this;
    }

    /**
     * Remove groupTrips
     *
     * @param \AppBundle\Entity\GroupUser $groupTrips
     */
    public function removeGroupTrip(\AppBundle\Entity\GroupUser $groupTrips)
    {
        $this->groupTrips->removeElement($groupTrips);
    }

    /**
     * Get groupTrips
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroupTrips()
    {
        return $this->groupTrips;
    }
}

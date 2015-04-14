<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SocialType
 *
 * @ORM\Table(name="tr_social_type")
 * @ORM\Entity
 */
class SocialType
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
     * @ORM\Column(name="name", type="string", length=32)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=32)
     */
    private $code;

	/**
	 * @ORM\OneToMany(targetEntity="SocialLogin", mappedBy="type")
	 */
	private $socialAccounts;
	
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
     * Set name
     *
     * @param string $name
     * @return SocialType
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
     * Set code
     *
     * @param string $code
     * @return SocialType
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->socialAccounts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add socialAccounts
     *
     * @param \AppBundle\Entity\SocialLogin $socialAccounts
     * @return SocialType
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

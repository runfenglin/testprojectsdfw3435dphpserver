<?php
/**
 * ApiKey User Provider
 * author: Haiping Lu
 */
namespace AppBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
//use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

use Symfony\Component\DependencyInjection\Container;

use AppBundle\Entity\User;

class ApiKeyUserProvider implements UserProviderInterface
{
    protected $_container;

    public function __construct(Container $container)
    {
        $this->_container = $container;
    }

    public function getUsernameForApiKey($apiKey)
    {
        // Look up the username based on the token in the database
        $username = '';
        
        return $username;
    }
    
    public function loadUserByApiKey($apiKey)
    {
        $em = $this->_container->get('doctrine')->getManager();
        return $em->getRepository('AppBundle:User')->getUserByApiKey($apiKey);
    }
    
    public function loadUserByUsername($username)
    {
        return new User($username, null, array('ROLE_USER'));
    }
    
    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException();
    }
    
    public function supportsClass($class)
    {
        //return 'Symfony\Component\Security\Core\User\User' === $class;
        return 'AppBundle\Entity\User' == $class;
    }
}
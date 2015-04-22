<?php
namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SocialService
{
    protected $_container;
    
    public function __construct(Container $container) {
        $this->_container = $container;
    }
    
    /** 
    * Verify facebook access token
    *
    * @param string $token (facebook access token) 
    * @return string 
    */ 
    public function verifyFacebookToken($token)
    {
        $entryPoint = $this->_container->getParameter('facebook') . '/me';

        $data = array(
            'fields' => 'name,email',
            'access_token' => $token
        );
        
        $curlService = $this->_container->get('curl.service');
        if (200 != $curlService->curlGet($entryPoint, $data))
        {
            throw new AccessDeniedException("Invalid Facebook Access Token");
        }
        
        return json_decode($curlService->getResult());
    }
    
    public function getFacebookFriendList($facebookId, $facebookToken)
    {
        $entryPoint = $this->_container->getParameter('facebook');
        $entryPoint .= '/' . $facebookId . '/friends';
        
        $data = array(
            'access_token' => $facebookToken
        );
    
        $curlService = $this->_container->get('curl.service');
        $code = $curlService->curlGet($entryPoint, $data);
        if (200 != $code)
        {
            $result = json_decode($curlService->getResult());

            throw new \Exception($result->error->message, $code);
        }
        
        //Here, we need return array to controller, instead of object.
        return json_decode($curlService->getResult(), TRUE);
    }
}
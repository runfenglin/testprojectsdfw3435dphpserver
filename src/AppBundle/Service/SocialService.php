<?php
namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints as Constraint;

use AppBundle\Entity\SocialType;

class SocialService
{
    CONST FACEBOOK_TOKEN_VERIFICATION = '/me';
    CONST FACEBOOK_FRIENDS_ENTRYPOINT = '/v2.3/me/friends';
    
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
        $entryPoint = $this->_container->getParameter(SocialType::FACEBOOK) . self::FACEBOOK_TOKEN_VERIFICATION;

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
    
    public function getFacebookFriendIds($token)
    {
        $friendId = array();
        
        while ($friends = $this->getFacebookFriendList($token, FALSE)) {
            if (empty($friends->data)) {
                return $friendId;
            }
            
            foreach($friends->data as $friend) {
                $friendId[] = $friend->id;
            }
            
            // Now, $token become a paging URL 
            $token = $friends->paging->next;
        }
    }
    
    /**
     * @param $facebookToken, either a token string, or a paging URL address
     * @return array|object
     */
    public function getFacebookFriendList($facebookToken, $array = TRUE)
    {
        $curlService = $this->_container->get('curl.service');
        
        //$facebookToken can be a paging URL
        $entryPoint = $facebookToken;
        $data = array();
        
        $urlConstraint = new Constraint\Url();
        $urlConstraint->message = 'Invalid url';
        if($this->_container->get('validator')->validateValue($entryPoint, $urlConstraint)->count()) 
        {
            // token, but not url
            $entryPoint = $this->_container->getParameter(SocialType::FACEBOOK);
   
            $entryPoint .= self::FACEBOOK_FRIENDS_ENTRYPOINT;
            $data['access_token'] = $facebookToken;
        }
    
        $code = $curlService->curlGet($entryPoint, $data);

        if (200 != $code)
        {
            $result = json_decode($curlService->getResult());

            throw new \Exception($result->error->message, $code);
        }
        
        return json_decode($curlService->getResult(), $array);
    }
}
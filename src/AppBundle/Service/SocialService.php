<?php
namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints as Constraint;

use AppBundle\Entity\SocialType;

class SocialService
{
    CONST FACEBOOK_TOKEN_VERIFICATION = '/me';
	CONST FACEBOOK_PROFILE_PICTURE = '/%IDENTITY%/picture';
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
            $error = $this->_container->get('translator')->trans('login.facebook.tokin.invalid');
            throw new AccessDeniedException($error);
        }
        
        return json_decode($curlService->getResult());
    }

   /** 
    * get facebook profile picture
    *
    * @param string $identity (facebook id or nickname) 
    * @return string 
    */     
	public function getFacebookProfilePicture($identity)
	{
		$entryPoint = $this->_container->getParameter(SocialType::FACEBOOK)
		              . str_replace('%IDENTITY%', $identity, self::FACEBOOK_PROFILE_PICTURE);
		
		$curlService = $this->_container->get('curl.service');
		
		if (200 != $curlService->curlGet($entryPoint))
		{
			$error = $this->_container->get('translator')->trans('login.facebook.profile.pic.invalid');
            throw new AccessDeniedException($error);
		}
		var_dump($curlService->getResult());die;
		return $curlService->getResult();
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
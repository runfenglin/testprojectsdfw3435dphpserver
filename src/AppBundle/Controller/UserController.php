<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
#use Acme\DemoBundle\Model\UserQuery;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use FOS\UserBundle\Model\UserInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations AS Rest;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class UserController extends FOSRestController
{
    /**
     * Get User Profile
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get user profile",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure"
     *   }
     * )
     * @Rest\Get("/profile")
     * @Rest\View()
     *
     * @return JSON
     */
    public function profileAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        $socialAccounts = array();
        $friends = array();
		
        $socialAccounts['count'] = $user->getSocialAccounts()->count();
        $socialAccounts['data'] = array();
        
        foreach($user->getSocialAccounts() as $key => $account) {
            $social = array(
                'type' => $account->getType()->getName(),
                'sm_name' => $account->getSmName(),
                'sm_email' => $account->getSmEmail(),
                'sm_token' => $account->getSmToken(),
                'created' => $account->getCreated()->getTimestamp(),
            ); 
            $socialAccounts['data'][] = $social;
        }
        
		$friends['count'] = $user->getMyFriends()->count();
		$friends['data'] = array();
		
		foreach($user->getMyFriends() as $key => $friend) {
            $item = array(
                'name' => $friend->getName(),
                'username' => $friend->getUsername(),
                'avatar' => '',
                'created' => $friend->getCreated()->getTimestamp(),
            ); 
            $friends['data'][] = $item;
        }
        $data = array(
            'apikey' => $user->getToken()->getKey(),
            'username' => $user->getUsername(),
            'name' => $user->getName(),
            'phone' => $user->getPhone(),
            'email' => $user->getEmail(),
            'created' => $user->getCreated()->getTimestamp(),
            'socialAcccounts' => $socialAccounts,
			'friends' => $friends
        );
        
        return array('result' => $data);
    }
    
}
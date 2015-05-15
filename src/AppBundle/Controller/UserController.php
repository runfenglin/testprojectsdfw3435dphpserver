<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Activity;

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
     * Get Latest Update
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get latest update",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure"
     *   }
     * )
     * @Rest\Get("/latest/update")
     * @Rest\View()
     *
     * @return JSON
     */
    public function latestUpdateAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        return $this->container
                    ->get('app.user.model')
                    ->setEntity($user)
                    ->getLatestUpdate();
        
        
    }
    
    /**
     * User Likes Activity
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "User likes activity",
     *   requirements = {
     *     {"name"="id", "dataType"="integer", "required"=true, "description"="Activity ID"}
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure"
     *   }
     * )
     * @Rest\Get("/like/{id}")
     * @Rest\View()
     *
     * @return JSON
     */
    public function likeAction(Request $request, $id)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        $em = $this->getDoctrine()->getManager();
        
        $activity = $em->getRepository('AppBundle:Activity')->find($id);
        
        // Only like activity, but not comment
        if ($activity 
            && $user->getId() != $activity->getUserId() 
                && !$activity->getParent()) {
            
            $alreadyLike = $user->getLikes()->filter(function($e) use($id) {
                return $e->getId() == $id;
            });
            
            if (!$alreadyLike->count()){
                $user->addLike($activity);
            }
            $em->persist($user);
            $em->flush();
        }
        
        return array(
            'user_id' => $user->getId(),
            'activity_id' => $id,
            'count' => $activity->getLikeByUsers()->count() 
        );
    }
    
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
        
        $friends['count'] = $user->getMyFriends()->count() + $user->getFriendsWithMe()->count();
        $friends['data'] = array();
        
        foreach($user->getMyFriends() as $key => $friend) {
            $item = array(
                'name' => $friend->getName(),
                'username' => $friend->getUsername(),
                'avatar' => $friend->base64EncodedAvatar(),
                'created' => $friend->getCreated()->getTimestamp(),
            ); 
            $friends['data'][] = $item;
        }
        
        foreach($user->getFriendsWithMe() as $key => $friend) {
            $item = array(
                'name' => $friend->getName(),
                'username' => $friend->getUsername(),
                'avatar' => $friend->base64EncodedAvatar(),
                'created' => $friend->getCreated()->getTimestamp(),
            ); 
            $friends['data'][] = $item;
        }
        $data = array(
            'username' => $user->getUsername(),
            'name' => $user->getName(),
            'phone' => $user->getPhone(),
            'email' => $user->getEmail(),
            'avatar' => $user->base64EncodedAvatar(),
            'created' => $user->getCreated()->getTimestamp(),
            'socialAcccounts' => $socialAccounts,
            'friends' => $friends
        );
        
        return array('result' => $data);
    }
    
}
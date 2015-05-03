<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\SocialType;
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

class FriendController extends FOSRestController
{
    /**
     * Get Facebook Friend List
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get facebook friend list",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure",
     *     403 = "Returned when token verification failure"
     *   }
     * )
     * @Rest\Get("/facebook")
     * @Rest\View()
     *
     * @return JSON
     */
    public function facebookAction(Request $request)
    {
        $type = SocialType::FACEBOOK;
        
        $user = $this->container->get('security.context')->getToken()->getUser();

        $socialAccount = $user->getSocialAccountByType($type);
        
        if (!$socialAccount) {
            $error = $this->get('translator')->trans('friend.facebook.nonexist');
            return new JsonResponse(array("error" => $error), Response::HTTP_BAD_REQUEST);
        }
        
        try{
            $token = $socialAccount->getSmToken();
            $userId = $socialAccount->getSmId();
        
            $socialService = $this->container->get('social.service');
            return $socialService->getFacebookFriendList($token);
            
        }
        catch(\Exception $e) {
            return new JsonResponse(array("error" => $e->getMessage()), Response::HTTP_FORBIDDEN);
        }
    }
    
}
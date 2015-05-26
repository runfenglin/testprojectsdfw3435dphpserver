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

        return $this->container->get('app.user.model')
                               ->setEntity($user)
                               ->likeActivity($id);
        

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

        return $this->container->get('app.user.model')
                               ->exposeOne($user);
    }
    
    /**
     * Get User Checkin
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get user checkin",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure",
     *     403 = "Returned when token verification failure"
     *   }
     * )
     * @Rest\Get("/activity")
     * @Rest\View()
     *
     * @return JSON
     */
    public function activityAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        try{
            $em = $this->getDoctrine()->getManager();
        
        //  $checkins = $em->getRepository('AppBundle:Checkin')->getCheckinsByUser($user);
        
            $activities = $em->getRepository('AppBundle:Activity')->getActivitiesByUser($user);
    
            return $this->container->get('app.activity.model')->expose($activities);
            
        }
        catch(\Exception $e) {
            return new JsonResponse(array("error" => $e->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }
    
    /**
     * Update Device Token
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update device token",
     *   requirements = {
     *     {"name"="device_token", "dataType"="string", "requirement"="/.{64}/", "required"=true, "description"="Device Token"}
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure"
     *   }
     * )
     * @Rest\Post("/device/token")
     * @Rest\View()
     *
     * @return JSON
     */
    public function updateDeviceTokenAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $dToken = $request->request->get('device_token');
        $user->setDeviceToken($dToken);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        
        return new JsonResponse(array('success' => TRUE), Response::HTTP_OK);
    }
    
}
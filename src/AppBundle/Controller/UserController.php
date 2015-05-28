<?php

namespace AppBundle\Controller;

use AppBundle\Entity;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use AppBundle\Exception\InvalidFormException;
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
    
    /**
     * Get User Request
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get user request",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure"
     *   }
     * )
     * @Rest\Get("/request")
     * @Rest\View()
     *
     * @return JSON
     */
    public function getRequestAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $tripRequests = $em->getRepository('AppBundle:Trip')
                           ->getRideRequestByUser($user);
        
        return $this->get('app.trip.model')->expose($tripRequests);
    }
    
    /**
     * Accept Ride Requests
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Accept ride request",
     *   requirements = {
     *     {"name"="trip", "dataType"="integer", "required"=true, "description"="Request ID"},
     *     {"name"="departure", "dataType"="string", "requirement"="/.{3,128}/", "required"=true, "description"="Pickup location"},
     *     {"name"="destination", "dataType"="string", "requirement"="/.{3,128}/", "required"=true, "description"="Drop-off location"},
     *     {"name"="time", "dataType"="string", "requirement"="/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/", "required"=true, "description"="Dropup time"},
     *     {"name"="comment", "dataType"="string", "description"="More detail"},
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure"
     *   }
     * )
     * @Rest\Post("/accept/request")
     * @Rest\View()
     *
     * @return JSON
     */ 
    public function acceptRequestAction(Request $request)
    {
        try {
            $rideOfferModel = $this->container
                              ->get('app.ride.offer.model');
                            
            $rideOffer = $rideOfferModel->post($request->request->all());
            
            if ($rideOffer instanceof Entity\RideOffer) {
                
                return $rideOfferModel->exposeOne($rideOffer);
            }
        }
        catch (InvalidFormException $exception) {
            $errors = $this->container
                           ->get('form.service')
                           ->getErrorMessages($exception->getForm());
            
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
        }
        catch (\Exception $exception) {
            return new JsonResponse(array('error' => $exception->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }
    
}
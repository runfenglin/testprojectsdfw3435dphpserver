<?php
/**
 * User APIs
 * author: Haiping Lu
 */
namespace AppBundle\Controller;

use AppBundle\Entity;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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
                    ->resetBadge()
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
        
		if(!$dToken = $request->request->get('device_token', NULL)) {
			$error = $this->get('translator')->trans('device.token.empty');
			return new JsonResponse(array("error" => $error), Response::HTTP_BAD_REQUEST);
		}
		
        $user->setDeviceToken($dToken);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        
        return new JsonResponse(array('success' => TRUE), Response::HTTP_OK);
    }
    
    /**
     * Get User Own Request
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get user own request",
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
                           ->getRideRequestsByUser($user);
        
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
     *     {"name"="departureReference", "dataType"="string", "requirement"="/.{,255}/", "required"=true, "description"="Departure Google Place ID"},
     *     {"name"="destination", "dataType"="string", "requirement"="/.{3,128}/", "required"=true, "description"="Drop-off location"},
     *     {"name"="destinationReference", "dataType"="string", "requirement"="/.{,255}/", "required"=true, "description"="Destination Google Place ID"},
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
                
                return $rideOfferModel->pushNotification($rideOffer)
                                      ->exposeOne($rideOffer);
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
    
    /**
     * Get Ride Offers on specific request
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get ride offers on specific request",
     *   requirements = {
     *     {"name"="id", "dataType"="integer", "required"=true, "description"="Ride request ID"}
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure",
     *     403 = "Returned when token verification failure",
     *     403 = "Returned when ride requess access deny"   
     *   }
     * )
     * @Rest\Get("/request/{id}/offers")
     * @Rest\View()
     *
     * @return JSON
     */
    public function requestOfferAction(Request $request, $id)
    {
        try {
            $id = (int) $id;
            
            $user = $this->container->get('security.context')->getToken()->getUser();

            $em = $this->getDoctrine()->getManager();
            $tripRequest = $em->getRepository('AppBundle:Trip')
                           ->find($id);
            
            if (!$tripRequest) {
                $error = $this->get('translator')->trans('rideRequest.notfound');
                throw new NotFoundHttpException($error);
            }

            if (!$tripRequest->getUser()->isEqualTo($user)) {
                $error = $this->get('translator')->trans('rideRequest.access.deny');
                throw new AccessDeniedHttpException($error);
            }
            
            return $this->get('app.ride.offer.model')
                        ->expose($tripRequest->getRideOffers());
        }
        catch(\RuntimeException $exception) {
            return new JsonResponse(array('error' => $exception->getMessage()), $exception->getStatusCode());
        }
        catch(\Exception $exception) {
            return new JsonResponse(array('error' => $exception->getMessage()), $exception->getCode());
        }
        
    }
    
    /**
     * Pick Driver
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Pick driver",
     *   requirements = {
     *     {"name"="id", "dataType"="integer", "required"=true, "description"="Ride offer ID"}
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure",
     *     403 = "Returned when token verification failure"
     *   }
     * )
     * @Rest\Get("/pick/driver/{id}")
     * @Rest\View()
     *
     * @return JSON
     */
    public function pickDriverAction(Request $request, $id)
    {
        try {
            $id = (int) $id;
            
            $user = $this->container->get('security.context')->getToken()->getUser();

            $em = $this->getDoctrine()->getManager();
            $rideOffer = $em->getRepository('AppBundle:RideOffer')
                            ->find($id);
            
            if (!$rideOffer) {
                $error = $this->get('translator')->trans('rideOffer.notfound');
                throw new NotFoundHttpException($error);
            }
            
            $rideRequest = $rideOffer->getTrip();
            
            if (!$rideRequest->getUser()->isEqualTo($user)) {
                $error = $this->get('translator')->trans('rideOffer.access.deny');
                throw new AccessDeniedHttpException($error);
            }
            
            return $this->get('app.trip.model')
                        ->setEntity($rideRequest)
                        ->pickOffer($rideOffer)
                        ->pushPickNotification()
                        ->expose();
        }
        catch(\RuntimeException $exception) {
            return new JsonResponse(array('error' => $exception->getMessage()), $exception->getStatusCode());
        }
        catch(\Exception $exception) {
            return new JsonResponse(array('error' => $exception->getMessage()), $exception->getCode());
        }
        
    }
    
    /**
     * Group Trips User Has Joined
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Group trip user has joined",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure"
     *   }
     * )
     * @Rest\Get("/grouptrip/joined")
     * @Rest\View()
     *
     * @return JSON
     */
    public function getJoinedGroupTripAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $joinedGroupTrips = $em->getRepository('AppBundle:Trip')
                               ->getJoinedGroupTrips($user);
        
        return $this->get('app.trip.model')->expose($joinedGroupTrips);
    }
    
    /**
     * Get Group Trips Created By User
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get group trips created by user",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure"
     *   }
     * )
     * @Rest\Get("/grouptrip")
     * @Rest\View()
     *
     * @return JSON
     */
    public function getMyGroupTripAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $groupTrips = $em->getRepository('AppBundle:Trip')
                         ->getGroupTripsByUser($user);
        
        return $this->get('app.trip.model')->expose($groupTrips);
    }
    
    /**
     * Get Paired Trips Created By User
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get paired trips created by user",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure"
     *   }
     * )
     * @Rest\Get("/trip")
     * @Rest\View()
     *
     * @return JSON
     */
    public function getTripAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $groupTrips = $em->getRepository('AppBundle:Trip')
                         ->getTripsByUser($user);
        
        return $this->get('app.trip.model')->expose($groupTrips);
    }
    
    /**
     * Join Group Trip
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Join group trip",
     *   requirements = {
     *     {"name"="id", "dataType"="integer", "required"=true, "description"="Group Trip ID"}
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure"
     *   }
     * )
     * @Rest\Get("/join/grouptrip/{id}")
     * @Rest\View()
     *
     * @return JSON
     */
    public function joinGroupTripAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $groupTrip = $em->getRepository('AppBundle:Trip')->find($id);
        
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        // TODO: verify if user is qualified to join this group trip
        if ($groupTrip 
            && $groupTrip->getGroup()) {

            if (!$groupTrip->getGroupUsers()
                          ->filter(function($e) use ($user) {
                              return $e->getUser()->isEqualTo($user);
                          })->count()
            ){
                $groupUser = new Entity\GroupUser();
                $groupUser->setTrip($groupTrip);
                $groupUser->setUser($user);
                $groupUser->setRole(Entity\GroupUser::ROLE_MEMBER);
                $groupTrip->addGroupUser($groupUser);
                $em->persist($groupTrip);
                $em->flush();
                
                return array('success' => true);
            }
            else {
                return new JsonResponse(
                    array('error' => $this->get('translator')
                                          ->trans('user.join.trip.already')
                    ), Response::HTTP_BAD_REQUEST);
            }
        }
        return new JsonResponse(array("error" => $this->get('translator')->trans('user.join.trip.forbidden')), Response::HTTP_FORBIDDEN);
    }
}
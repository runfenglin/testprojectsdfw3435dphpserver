<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Exception\InvalidFormException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use FOS\UserBundle\Model\UserInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations AS Rest;

use AppBundle\Entity As Entity;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class TripController extends FOSRestController
{
    /**
     * Create a Trip
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a trip",
     *   requirements = {
     *     {"name"="group", "dataType"="boolean", "required"=true, "description"="Group trip or not"},    
     *     {"name"="departure", "dataType"="string", "requirement"="/.{3,128}/", "required"=true, "description"="Pickup location"},
     *     {"name"="departureReference", "dataType"="string", "requirement"="/.{,255}/", "required"=true, "description"="Departure Google Place ID"},
     *     {"name"="destination", "dataType"="string", "requirement"="/.{3,128}/", "required"=true, "description"="Drop-off location"},
     *     {"name"="destinationReference", "dataType"="string", "requirement"="/.{,255}/", "required"=true, "description"="Destination Google Place ID"},
     *     {"name"="time", "dataType"="string", "requirement"="/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/", "required"=true, "description"="Dropup time"},
     *     {"name"="comment", "dataType"="string", "description"="More detail"},
     *     {"name"="visibility", "dataType"="integer", "description"="Send invitation to"},
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure",
     *     403 = "Returned when token verification failure"
     *   }
     * )
     * @Rest\Post("/create")
     * @Rest\View()
     *
     * @return JSON
     */
    public function createAction(Request $request)
    {
        try {
            $tripModel = $this->container
                              ->get('app.trip.model');
                            
            $trip = $tripModel->post($request->request->all());
            
            if ($trip instanceof Entity\Trip) {
                
                return $tripModel->expose($trip);
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
     * Get Trip Requests of Friends and Friends' Friends
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get trip requests of friends and friends' friends",   
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure",
     *     403 = "Returned when token verification failure"
     *   }
     * )
     * @Rest\Get("/request")
     * @Rest\View()
     *
     * @return JSON
     */
	public function requestAction(Request $request)
	{
		$user = $this->container->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $friendRequests = $em->getRepository('AppBundle:Trip')
                           ->getFriendRequestByUser($user);
        
        return $this->get('app.trip.model')->expose($friendRequests);
	}
	
}
<?php
/**
 * Admin Trip APIs
 * author: Haiping Lu
 */
namespace AdminApiBundle\Controller;

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

class TripController extends FOSRestController
{
    /**
     * Get Trips Requests
     *
     * @Rest\Get("/request")
     * @Rest\View()
     *
     * @return JSON
     */
    public function queryTripRequestsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$tripRequests = $em->getRepository('AppBundle:Trip')->getTripRequests();
		return $this->get('app.trip.model')->expose($tripRequests);
    }
    
	/**
     * Get Paired Trips
     *
     * @Rest\Get("/single")
     * @Rest\View()
     *
     * @return JSON
     */
    public function queryPairedTripsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$paired = $em->getRepository('AppBundle:Trip')->getPairedTrips();
		return $this->get('app.trip.model')->expose($paired);
    }
	
	/**
     * Get Paired Trips
     *
     * @Rest\Get("/group")
     * @Rest\View()
     *
     * @return JSON
     */
    public function queryGroupTripsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$groups = $em->getRepository('AppBundle:Trip')->getGroupTrips();
		return $this->get('app.trip.model')->expose($groups);
    }
	
	/**
     * Get Group Trip Detail
     *
     * @Rest\Get("/group/{id}")
     * @Rest\View()
     *
     * @return JSON
     */
    public function getGroupTripDetailAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
		$group = $em->getRepository('AppBundle:Trip')->getGroupTripDetail($id);
		return $this->get('app.trip.model')->exposeOne($group);
    }
    
}
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

use AppBundle\Entity\Checkin;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class CheckinController extends FOSRestController
{
    /**
	 * This Action has been moved to User::CheckinAction
	 *
     * Get Checkin List
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get user checkin list",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure",
     *     403 = "Returned when token verification failure"
     *   }
     * )
     * @Rest\Get("/list")
     * @Rest\View()
     *
     * @return JSON
     *
    public function listAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        try{
            $em = $this->getDoctrine()->getManager();
            $checkins = $em->getRepository('AppBundle:Checkin')
                           ->findBy(array('user' => $user), array('created' => 'DESC'));
            
            return $this->container->get('app.activity.model')->expose($checkins);
            
        }
        catch(\Exception $e) {
            return new JsonResponse(array("error" => $e->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }
     */
	 
    /**
     * Get Specific Checkin
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get specific checkin",
     *   requirements = {
     *     {"name"="id", "dataType"="integer", "required"=true, "description"="Checkin ID"} 
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     403 = "Returned when token verification failure",
     *     404 = "Returned when checkin does not exist"
     *   }
     * )
     * @Rest\Get("/get/{id}")
     * @Rest\View()
     *
     * @return JSON
     */
    public function getAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $checkin = $em->getRepository('AppBundle:Checkin')->find($id);
        
        if (!$checkin) {
            $error = $this->get('translator')->trans('activity.checkin.not.found');
            return new JsonResponse(array('error' => $error), Response::HTTP_NOT_FOUND);
        }
        
        return $this->container
                    ->get('app.activity.model')
                    ->exposeOne($checkin);
    }
    
    /**
     * Create a Checkin
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create user checkin",
     *   requirements = {
     *     {"name"="checkinReference", "dataType"="string", "requirement"="/.{,255}/", "required"=true, "description"="Google Place ID"},
     *     {"name"="checkinName", "dataType"="string", "requirement"="/.{3,128}/", "required"=true, "description"="Google Place Name"}, 
     *     {"name"="comment", "dataType"="string", "requirement"="/.{3,255}/", "required"=false, "description"="Comment"}  
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
            $activityModel = $this->container
                                  ->get('app.activity.model');
                            
            $checkin = $activityModel->postCheckin($request->request->all());
            
            if ($checkin instanceof Checkin) {
                
                return $activityModel->expose($checkin);
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
     * Update Checkin.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update checkin",
     *   requirements = {
     *     {"name"="id", "dataType"="integer", "required"=true, "description"="Checkin ID"},
     *     {"name"="checkinReference", "dataType"="string", "requirement"="/.{,255}/", "required"=true, "description"="Google Place ID"},
     *     {"name"="checkinName", "dataType"="string", "requirement"="/.{3,128}/", "required"=true, "description"="Google Place Name"}, 
     *     {"name"="comment", "dataType"="string", "requirement"="/.{3,255}/", "required"=false, "description"="Comment"}  
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure",
     *     403 = "Returned when Returned when token verification or permission failed",
     *     404 = "Returned when checkin does not exist"
     *   }
     * )
     * @Rest\Put("/update/{id}")
     * @Rest\View()
     *
     * @return JSON
     */ 
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $checkin = $em->getRepository('AppBundle:Checkin')->find($id);
        
        if (!$checkin) {
            $error = $this->get('translator')->trans('activity.checkin.invalid');
            return new JsonResponse(array('error' => $error), Response::HTTP_BAD_REQUEST);
        }
        
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        if(!$user->isEqualTo($checkin->getUser())) {
            $error = $this->get('translator')->trans('activity.checkin.update.deny');
            return new JsonResponse(array('error' => $error), Response::HTTP_FORBIDDEN);
        }
        
        try {

            $activityModel = $this->container
                                  ->get('app.activity.model')
                                  ->setEntity($checkin);
                            
            $checkin = $activityModel->postCheckin(
                                           $request->request->all(),
                                           $request->getMethod()
                                       );
            
            if ($checkin instanceof Checkin) {
                
                return $activityModel->expose($checkin);
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
     * Delete Checkin.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Delete checkin",
     *   requirements = {
     *     {"name"="id", "dataType"="integer", "required"=true, "description"="Checkin ID"} 
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure",
     *     403 = "Returned when Returned when permission failed",
     *     404 = "Returned when checkin does not exist"
     *   }
     * )
     * @Rest\Delete("/delete/{id}")
     * @Rest\View()
     *
     * @return JSON
     */      
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $checkin = $em->getRepository('AppBundle:Activity')->find($id);
        
        if (!$checkin) {
            $error = $this->get('translator')->trans('activity.checkin.invalid');
            return new JsonResponse(array('error' => $error), Response::HTTP_NOT_FOUND);
        }
        
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        if(!$user->isEqualTo($checkin->getUser())) {
            $error = $this->get('translator')->trans('activity.checkin.update.deny');
            return new JsonResponse(array('error' => $error), Response::HTTP_FORBIDDEN);
        }
        
        $em->remove($checkin);
        $em->flush();
        
        return new JsonResponse(array('success' => TRUE), Response::HTTP_OK);
    }
}
<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use FOS\UserBundle\Model\UserInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations AS Rest;

use AppBundle\Entity\Comment;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class CommentController extends FOSRestController
{ 
    /**
     * Create a Comment
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create user comment",
     *   requirements = {
     *     {"name"="parent", "dataType"="integer", "required"=true, "description"="Target activity"},    
     *     {"name"="comment", "dataType"="string", "requirement"="/.{3,255}/", "required"=true, "description"="Comment content"}
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
                            
            $comment = $activityModel->postComment($request->request->all());
            
            if ($comment instanceof Comment) {
                
                return $activityModel->expose($comment);
            }
        }
        catch (InvalidFormException $exception) {
            $errors = $this->container
                           ->get('form.service')
                           ->getErrorMessages($exception->getForm());
            
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
        }
        catch (\Exception $exception) {
            return new JsonResponse(array('message' => $exception->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }
    
    /**
     * Update Comment.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update comment",
     *   requirements = {
     *     {"name"="id", "dataType"="integer", "required"=true, "description"="Comment ID"},
     *     {"name"="comment", "dataType"="string", "requirement"="/.{3, 255}/", "required"=true, "description"="Comment"}  
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure",
     *     403 = "Returned when Returned when token verification or permission failed",
     *     404 = "Returned when comment does not exist"
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
        $comment = $em->getRepository('AppBundle:Comment')->find($id);
        
        if (!$comment) {
            return new JsonResponse(array('message' => 'Invalid comment'), Response::HTTP_NOT_FOUND);
        }
        
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        if(!$user->isEqualTo($comment->getUser())) {
            return new JsonResponse(array('message' => 'Only creator can update this comment'), Response::HTTP_FORBIDDEN);
        }

        try {

            $activityModel = $this->container
                                  ->get('app.activity.model')
                                  ->setEntity($comment);
                           
            $comment = $activityModel->postComment(
                                        $request->request->all(), 
                                        $request->getMethod()
                                     );
            
            if ($comment instanceof Comment) {
                return $activityModel->expose($comment);
            }

        }
        catch (InvalidFormException $exception) {
            $errors = $this->container
                           ->get('form.service')
                           ->getErrorMessages($exception->getForm());
            
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
        }
        catch (\Exception $exception) {
            return new JsonResponse(array('message' => $exception->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }
    
    /**
     * Delete Comment.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Delete comment",
     *   requirements = {
     *     {"name"="id", "dataType"="integer", "required"=true, "description"="Comment ID"} 
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure",
     *     403 = "Returned when Returned when permission failed",
     *     404 = "Returned when comment does not exist"
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
        $comment = $em->getRepository('AppBundle:Comment')->find($id);
        
        if (!$comment) {
            return new JsonResponse(array('message' => 'Invalid comment'), Response::HTTP_NOT_FOUND);
        }
        
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        if(!$user->isEqualTo($comment->getUser())) {
            return new JsonResponse(array('message' => 'Only creator can delete this comment'), Response::HTTP_FORBIDDEN);
        }
        
        $em->remove($comment);
        $em->flush();
        
        return new JsonResponse(array('success' => TRUE), Response::HTTP_OK);
    }
}
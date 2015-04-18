<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations AS Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use AppBundle\Entity\User;
use AppBundle\Exception\InvalidFormException;

class RegisterController extends FOSRestController
{
    /**
     * Register with Phone Number.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = ""
     *   }
     * )
     * @Rest\Post("/register")
     * @Rest\View()
     *
     * @return JSON
     */
    public function registerAction(Request $request)
    {
        try {
            $user = $this->container
                         ->get('app.user.model')
                         ->post($request->request->all());
            
            if ($user instanceof User) {
                return array('apikey' => $user->getToken()->getKey());
            }
        }
        catch (InvalidFormException $exception) {
            $errors = $this->container->get('form.service')->getErrorMessages($exception->getForm());
            
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
        }
        catch (\Exception $exception) {
            return new JsonResponse(array('message' => $exception->getMessage()), Response::HTTP_BAD_REQUEST);
        }
        
    }

}

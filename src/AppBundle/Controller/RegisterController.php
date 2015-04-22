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
     *   requirements = {
     *     {"name"="name", "dataType"="string", "requirement"="/.+/", "required"=true, "description"="display name"},
     *     {"name"="username", "dataType"="string", "requirement"="/.+/", "required"=true, "description"="unique user ID"},
     *     {"name"="country", "dataType"="string", "requirement"="/[0-9\-]{2, 5}/", "required"=true, "description"="country code"},
     *     {"name"="phone", "dataType"="string", "requirement"="/[0-9]+/", "required"=true, "description"="phone number"},
     *     {"name"="password[password]", "dataType"="string", "requirement"="/[0-9a-z\-\_]{6,20}/i", "required"=true, "description"="password"},
     *     {"name"="password[confpass]", "dataType"="string", "requirement"="/[0-9a-z\-\_]{6,20}/i", "required"=true, "description"="confirmed password"}   
     *   },
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

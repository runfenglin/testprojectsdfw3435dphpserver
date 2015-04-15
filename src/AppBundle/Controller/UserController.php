<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
#use Acme\DemoBundle\Model\UserQuery;

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
     * Create a User from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new user",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when failure"
     *   }
     * )
     * @Rest\Post("/")
     * @Rest\View()
     *
     * @param Request $request the request object
     *
     * @return JSON
     */
    public function createUserAction(Request $request)
    {
		$myself = $this->getUser();
        
		if (!is_object($myself) || !$myself instanceof UserInterface) {
			return new JsonResponse(array('message' => 'You do not have access to this section.'), Response::HTTP_UNAUTHORIZED);
        }
	
		if ($myself->isSuperAdmin()) {
			
			return $this->container
						->get('site.api.user.handler')
						->post($request->request->all());
		}
		else {
			return new JsonResponse(array('message' => 'You do not have access to this section.'), Response::HTTP_FORBIDDEN);
		}
    }
	
}
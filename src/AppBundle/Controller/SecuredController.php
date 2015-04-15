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

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class SecuredController extends FOSRestController
{
	/**
     * Login.
     *
     * @ApiDoc(
     *   resource = false
     * )
     * @Rest\Get("/login")
     * @Rest\View()
     *
     * @param Request $request the request object
     *
     * @return JSON
     */
    public function loginAction(Request $request)
    {
        return array('id' => 'who.am.i');
    }


	/**
     * Login.
     *
     * @ApiDoc(
     *   resource = false
     * )
     * @Rest\Get("/logout")
     * @Rest\View()
     *
     * @param Request $request the request object
     *
     * @return JSON
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }
}

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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use AppBundle\Entity\User;
use AppBundle\Entity\Token;

class LogoutController extends FOSRestController
{
	/**
     * Logout.
     *
     * @ApiDoc(
     *   resource = false,
	 *   statusCodes = {
     *     200 = "Returned when successful",
	 *     400 = ""
	 *   }
     * )
     * @Rest\Get("/logout")
     * @Rest\View()
     *
     * @return JSON
     */
    public function logoutAction()
    {
        
    }
}

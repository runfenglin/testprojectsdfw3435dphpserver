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

class DefaultController extends FOSRestController
{
	/**
     * Api Test.
     *
     * @ApiDoc(
     *   resource = true
     * )
     * @Rest\Get("/example")
     * @Rest\View()
     *
     * @param Request $request the request object
     *
     * @return JSON
     */
    public function indexAction(Request $request)
    {
		if (!$query = $request->query->all()) {
			$query = array('All parameters will be returned as JSON format if you append some GET parameters to this URL'); 
		}
		return new JsonResponse($query);
    //  return $this->render('default/index.html.twig');
    }
}

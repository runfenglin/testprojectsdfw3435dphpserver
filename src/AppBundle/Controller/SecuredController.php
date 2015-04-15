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

class SecuredController extends FOSRestController
{
	/**
     * Login.
     *
     * @ApiDoc(
     *   resource = false
     * )
     * @Rest\Post("/login")
     * @Rest\View()
     * @url: http://stackoverflow.com/questions/22723927/simple-api-key-authentication-in-symfony2-using-fosuserbundle-and-hwioauthbundl
     * @param Request $request the request object
     *
     * @return JSON
     */
    public function loginAction(Request $request)
    {
        $token = $request->get('token', NULL);
		$phone = $request->get('phone', NULL);
		$password = $request->get('password', NULL);
		$country = $request->get('country', 64);
		
		if ($token) {
			// SM login, verify token
		}
		else if ($phone && $password) {
			// phone login
		}
		else {
			return new Response("You must pass login information", Response::HTTP_BAD_REQUEST);
		}
		
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('AppBundle:User')->getUserByPhone($phone, $country);
		
		if (!$user instanceof User) {
			return new Response("Bad Credential", Response::HTTP_FORBIDDEN);
		}
		
		$encoder = $this->get('security.encoder_factory')
		                ->getEncoder($user);
		
		$encodedPass = $encoder->encodePassword($password, $user->getSalt());
		
		if($encodedPass != $this->getPassword()) {
			return new Response("Bad Credential", Response::HTTP_FORBIDDEN);
		}
		
		$apiKey = uniqid(NULL, TRUE);
		
		return array('apikey' => $user->getToken()->getKey());
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

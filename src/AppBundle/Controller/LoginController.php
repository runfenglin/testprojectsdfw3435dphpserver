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
use AppBundle\Entity\SocialLogin;

//use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
//use Symfony\Component\Validator\Constraints\Country as CountryConstraint;
//use Symfony\Component\Validator\Constraints\Regex as RegexConstraint;

use Symfony\Component\Validator\Constraints as Constraint;
class LoginController extends FOSRestController
{
	/**
     * Login with Phone Number.
     *
     * @ApiDoc(
     *   resource = true,
	 *   statusCodes = {
     *     200 = "Returned when successful",
	 *     400 = "Returned when missing one of phone, password and country code",
     *     403 = "Returned when authentication failure"
	 *   }
     * )
     * @Rest\Post("/phone")
     * @Rest\View()
     *
     * @return JSON
     */
    public function phoneAction(Request $request)
    {
		// http://stackoverflow.com/questions/22723927/simple-api-key-authentication-in-symfony2-using-fosuserbundle-and-hwioauthbundl

		$phone = $request->request->get('phone', NULL);
		$password = $request->request->get('password', NULL);
		$country = $request->request->get('country', 64);
		
		if ($phone && $password && $country) {
			// phone login

			// verify country
			$countryConstraint = new Constraint\Country();
			$countryConstraint->message = 'Invalid country code';
			
			if ($this->get('validator')->validateValue($country, $countryConstraint))
			{
				return new Response("Invalid country code", Response::HTTP_BAD_REQUEST);
			}
			
			// verfiy phone
			$regexConstraint = new Constraint\Regex();
			$regexConstraint->message = 'Invalid phone number';
			$regexConstraint->pattern = '/\d+/';
			
			if ($this->get('validator')->validateValue($country, $countryConstraint))
			{
				return new Response("Invalid phone number", Response::HTTP_BAD_REQUEST);
			}
			 
			$em = $this->getDoctrine()->getManager();
			$user = $em->getRepository('AppBundle:User')
			           ->getUserByPhoneLogin($phone, $country);
			
			if (!$user instanceof User) {
				return new Response("Bad Credential", Response::HTTP_FORBIDDEN);
			}
			
			$encoder = $this->get('security.encoder_factory')
							->getEncoder($user);
			
			$encodedPass = $encoder->encodePassword($password, $user->getSalt());
			
			if($encodedPass != $this->getPassword()) {
				return new Response("Bad Credential", Response::HTTP_FORBIDDEN);
			}
			
			$user->updateToken();
			
			$em->persist($user);
			$em->flush();
			
			return array('apikey' => $user->getToken()->getKey());
		}
		else {
			return new Response("You must pass country code, phone number and password", Response::HTTP_BAD_REQUEST);
		}
    }
	
	/**
     * Login with Facebook.
     *
     * @ApiDoc(
     *   resource = false,
	 *   statusCodes = {
     *     200 = "Returned when successful",
	 *     400 = "Returned when missing parameters",
     *     403 = "Returned when authentication failure"
	 *   }
     * )
     * @Rest\Post("/facebook")
     * @Rest\View()
     *
     * @return JSON
     */
    public function facebookAction(Request $request)
	{
		$token = $request->request->get('token', NULL);
		$email = $request->request->get('email', NULL);
		$username = $request->request->get('username', NULL);
		$type = 'facebook';
		
		if (!$token || !$email || !$username) 
		{
			return new Response("Incorrect parameters passed", Response::HTTP_BAD_REQUEST);
		}
		
		$emailConstraint = new Constraint\Email();
		$emailConstraint->message = 'Invalid email address';
		if($this->get('validator')->validateValue($email, $emailConstraint)) 
		{
			return new Response("Invalid email address", Response::HTTP_BAD_REQUEST);
		}
		
		//TODO verify token
		if((boolean) $token) {
			return new Response("Invalid token", Response::HTTP_BAD_REQUEST);
		}
	
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('AppBundle:User')
				   ->findOneBy(array('email' => $email));
		
		if ($user) {
			$socialAccount = $user->getSocialAccountByType($type);
			
			if ($socialAccount) {
				$socialAccount->setSmEmail($email);
				$socialAccount->setSmToken($token);
				$socialAccount->setSmUsername($username);
			}
			else {
				//TODO, is it possible?
			}
			// Should we update name to this social account name?
			$user->setName($username);
			$user->updateToken();

		}
		else {
			$user = new User();
			$user->setEmail($email);
			$user->setName($username);
			$user->updateToken();
			
			$socialType = $em->getRepository('AppBundle:SocialType')
							 ->findOneBy(array('code' => $type));
			
			// Also add this social account to social login table
			$socialAccount = new SocialLogin();
			$socialAccount->setSmUsername($username);
			$socialAccount->setSmToken($token);
			$socialAccount->setSmEmail($email);
			$socialAccount->setType($socialType);
			$socialAccount->setCreated(new \DateTime('@' . time()));
			$socialAccount->setUser($user);
			$user->addSocialAccount($socialAccount);
		}
		
		$em->persist($user);
		$em->flush();
		
		return array('apikey' => $user->getToken()->getKey());

	}
	
}

<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Acme\DemoBundle\Form\ContactType;
use Symfony\Component\HttpFoundation\JsonResponse;

// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AppBundle\Entity\Token;

class DemoController extends Controller
{
    /**
     * @Route("/", name="_demo")
     * @Template()
     */
    public function indexAction()
    {
		$router = $this->container->get('router');
		$collection = $router->getRouteCollection();
		$allRoutes = $collection->all();
		
		$routes = array();

		foreach ($allRoutes as $name => $params)
		{
			if (0 !== strpos($name, 'demo_')) {
				continue;
			}
			
			$defaults = $params->getDefaults();

			if (isset($defaults['_controller']))
			{
				$routes[$name]= $params->getPath();
			}
		}
		
	//	return new JsonResponse($routes);
        return array('routes' => $routes);
    }

    /**
     * @Route("/hello/{name}", name="_demo_hello")
     * @Template()
     */
    public function helloAction($name)
    {
        return array('name' => $name);
    }

    /**
     * @Route("/contact", name="_demo_contact")
     * @Template()
     */
    public function contactAction(Request $request)
    {
        $form = $this->createForm(new ContactType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $mailer = $this->get('mailer');

            // .. setup a message and send it
            // http://symfony.com/doc/current/cookbook/email.html

            $request->getSession()->getFlashBag()->set('notice', 'Message sent!');

            return new RedirectResponse($this->generateUrl('_demo'));
        }

        return array('form' => $form->createView());
    }

	/**
     * @Route("/friend/list", name="demo_facebook_friend_list")
     * @Template()
     */	
	public function friendListAction()
	{
		$em = $this->getDoctrine()->getManager();
		$tokens = $em->getRepository('AppBundle:Token')
				   ->findAll();
		return array('tokens' => $tokens);
	}
	
	/**
     * @Route("/profile", name="demo_profile")
     * @Template()
     */	
	public function profileAction()
	{
		$em = $this->getDoctrine()->getManager();
		$tokens = $em->getRepository('AppBundle:Token')
				   ->findAll();
		return array('tokens' => $tokens);
	}
	
	/**
     * @Route("/register", name="demo_registration")
     * @Template()
     */
    public function registerAction()
    {
        return array();
    }

	/**
     * @Route("/login/facebook", name="demo_login_facebook")
     * @Template()
     */
    public function loginFacebookAction()
    {
        return array();
    }
	
	/**
     * @Route("/login/phone", name="demo_login_phone")
     * @Template()
     */
    public function loginPhoneAction()
    {
        return array();
    }

	/**
     * @Route("/logout", name="demo_logout")
     * @Template()
     */
    public function logoutAction()
    {
		$em = $this->getDoctrine()->getManager();
		$tokens = $em->getRepository('AppBundle:Token')
				   ->findAll();
        return array('tokens' => $tokens);
    }

	/**
     * @Route("/profile/picture", name="demo_profile_picture")
     * @Template()
     */	
	public function profilePictureAction(Request $request)
	{
		if ($base64 = $request->request->get('base64', NULL)) {
		
			$data = base64_decode($base64);
			
			$tmp = tempnam(sys_get_temp_dir(), 'picture.jpg');
			
			$fp = fopen($tmp, "w+");
			fwrite($fp, $data);
			fclose($fp);
                
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			
			$mimeType = finfo_file($finfo, $tmp);
			
			header('Content-type: ' . $mimeType);
			header('Content-length: ' . filesize($tmp));
			header('Content-Disposition: filename="picture.jpg"');
			header('X-Pad: avoid browser bug');
			header('Cache-Control: no-cache');
			readfile($tmp);
			exit;
		}
		
		return array();
	}
}

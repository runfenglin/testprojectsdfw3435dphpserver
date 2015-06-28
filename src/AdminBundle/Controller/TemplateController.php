<?php
/**
 * Admin Template Page
 * author: Haiping Lu
 */
namespace AdminBundle\Controller;

use AppBundle\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use AppBundle\Exception\InvalidFormException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class TemplateController extends Controller
{
    /**
     * @Route("/tmpl/{tmpl}", name="admin_page_template", requirements={"tmpl"="layout|dashboard"})
     * @Template()
     */
    public function indexAction(Request $request, $tmpl)
    {
		$tmpl = strtolower($tmpl);
		
		switch($tmpl) {
			case 'trip':
			case 'layout':
			case 'dashboard': {
				return $this->render('AdminBundle:Template:' . $tmpl . '.html.twig');
			}
			default: {
				return new JsonResponse($tmpl . ' template is not found!', Response::HTTP_NOT_FOUND);
			}
		}
	}
	
	/**
     * @Route("/tmpl/trip/{tmpl}", name="admin_trip_template", requirements={"tmpl"="base|single|group|request|group-detail"})
     * @Template()
     */
    public function TripAction(Request $request, $tmpl)
    {
		$tmpl = strtolower($tmpl);
		
		switch($tmpl) {
			case 'base':
			case 'single':
			case 'group':
			case 'group-detail':
			case 'request': {
				return $this->render('AdminBundle:Template:Trip/' . $tmpl . '.html.twig');
			}
			default: {
				return new JsonResponse($tmpl . ' trip template is not found!', Response::HTTP_NOT_FOUND);
			}
		}
	}
	
	/**
     * @Route("/tmpl/user/{tmpl}", name="admin_user_template", requirements={"tmpl"="base|mobile|media"})
     * @Template()
     */
    public function UserAction(Request $request, $tmpl)
    {
		$tmpl = strtolower($tmpl);
		
		switch($tmpl) {
			case 'base':
			case 'mobile':
			case 'media':{
				return $this->render('AdminBundle:Template:User/' . $tmpl . '.html.twig');
			}
			default: {
				return new JsonResponse($tmpl . ' user template is not found!', Response::HTTP_NOT_FOUND);
			}
		}
	}
	
	/**
     * Template
     *
     * @Route("/directive/tmpl/{tmpl}", name="admin_directive_template", requirements={"tmpl"="login-info"})
     * @Template()
     */ 
    public function directiveAction(Request $request, $tmpl)
    {

        $tmpl = strtolower($tmpl);
		
		switch($tmpl) {
			case 'login-info':{
				return $this->render('AdminBundle:Template:Directives/' . $tmpl . '.html.twig');
			}
			default: {
				return new JsonResponse($tmpl . ' directive template is not found!', Response::HTTP_NOT_FOUND);
			}
		}
    }
}
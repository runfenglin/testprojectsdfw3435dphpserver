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
			case 'layout':
			case 'dashboard': {
				return $this->render('AdminBundle:Template:' . $tmpl . '.html.twig');
			}
			default: {
				return new JsonResponse(array('error' => $tmpl . ' template is not found!'), Response::HTTP_NOT_FOUND);
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
				return new JsonResponse(array('error' => $tmpl . ' directive template is not found!'), Response::HTTP_NOT_FOUND);
			}
		}
    }
}
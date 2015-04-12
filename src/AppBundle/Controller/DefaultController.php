<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/app/example", name="homepage")
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

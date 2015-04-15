<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class WelcomeController extends Controller
{
    public function indexAction()
    {
        /*
         * The action's view can be rendered using render() method
         * or @Template annotation as demonstrated in DemoController.
         *
         */
		$router = $this->container->get('router');
		$collection = $router->getRouteCollection();
		$allRoutes = $collection->all();
		
		$routes = array();

		foreach ($allRoutes as $name => $params)
		{
			if (0 === strpos($name, '_')) {
				continue;
			}
			
			$defaults = $params->getDefaults();

			if (isset($defaults['_controller']))
			{
				$routes[$name]= $params->getPath();
			}
		}

		return new JsonResponse($routes);
		 
        return $this->render('AcmeDemoBundle:Welcome:index.html.twig');
    }
	
}

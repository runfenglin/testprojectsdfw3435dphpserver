<?php
/**
 * Admin User APIs
 * author: Haiping Lu
 */
namespace AdminApiBundle\Controller;

use AppBundle\Entity;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use AppBundle\Exception\InvalidFormException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use FOS\UserBundle\Model\UserInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations AS Rest;

class UserController extends FOSRestController
{
    /**
     * Get User Self Data
     *
     * @Rest\Get("/self")
     * @Rest\View()
     *
     * @return JSON
     */
    public function mySelfAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        return array('username' => $user->getUsername(),'picture' => '/bundles/admin/images/avatar.png');
    }
    
    /**
     * Get Mobile Account Data
     *
     * @Rest\Get("/mobile")
     * @Rest\View()
     *
     * @return JSON
     */
    public function queryMobileAccountAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$users = $em->getRepository('AppBundle:User')->getMobileAccounts();
		return $this->get('app.user.model')->expose($users);
    }
	
	/**
     * Get Media Account Data
     *
     * @Rest\Get("/media")
     * @Rest\View()
     *
     * @return JSON
     */
    public function queryMediaAccountAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$users = $em->getRepository('AppBundle:User')->getMediaAccounts();
		return $this->get('app.user.model')->expose($users);
    }
}
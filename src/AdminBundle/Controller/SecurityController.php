<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AppBundle\Entity;

class SecurityController extends Controller
{
    /**
     * @Route("/secure/", name="secure")
     * @Template()
     */
    public function indexAction(Request $request)
    {	
		$token = $this->container->get('security.context')->getToken();
		
		if ($token instanceof TokenInterface) {
			if ($token->getUser() instanceof User) {
				return $this->redirect($this->generateUrl('admin'));
			}
		}

		return array();
	}	
	
	/**
     * Security template
     *
     * @Route("/secure/tmpl/{tmpl}", name="secure_template", requirements={"tmpl"="base|login"})
     * @Template()
     */ 
    public function tmplAction(Request $request, $tmpl)
    {

        $this->_view['tmpl'] = strtolower($tmpl);
		switch($tmpl) {
			case 'login': {
				$csrfToken = $this->has('form.csrf_provider')
							? $this->get('form.csrf_provider')
								   ->generateCsrfToken('authenticate')
							: null;
				$this->_view['csrf_token'] = $csrfToken;
				break;
			}
			default: {
				break;
			}
		}
        return $this->_view;
    }
	
    /**
     * Skin Site Login
     *
     * @Route("/secure/login/", name="secure_login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContextInterface::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('authenticate')
            : null;

        return $this->renderLogin(array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'csrf_token' => $csrfToken,
        ));
    }

    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderLogin(array $data)
    {
        return $this->render('AdminBundle:Security:login.html.twig', $data);
    }

    /**
     * @Route("/secure/check", name="secure_logincheck")
     */
    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }

    /**
     * @Route("/secure/logout/", name="secure_logout")
     */
    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }

	/**
     * Tell the user to check his email provider
     * @Route("/#/reset-password/{token}", name="skin_secure_reset_url")
     */
    public function resetPasswordUrlAction(Request $request)
    {
        return new Response('This url is only to generate reset password url in email', Response::HTTP_OK);
    }
}

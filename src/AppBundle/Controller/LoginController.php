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

use Symfony\Component\HttpFoundation\File\UploadedFile;

use AppBundle\Entity\User;
use AppBundle\Entity\Token;
use AppBundle\Entity\SocialType;
use AppBundle\Entity\SocialLogin;
use AppBundle\Entity\Media;

use Symfony\Component\Validator\Constraints as Constraint;
class LoginController extends FOSRestController
{
    /**
     * Login with Phone Number.
     *
     * @ApiDoc(
     *   resource = true,
     *   requirements = {
     *     {"name"="country", "dataType"="string", "requirement"="/[0-9\-]{2,5}/", "required"=true, "description"="country code"},
     *     {"name"="phone", "dataType"="string", "requirement"="/\d+/", "required"=true, "description"="phone number"},
     *     {"name"="password", "dataType"="string", "requirement"="/[0-9a-z\_\-]{6,20}/", "required"=true, "description"="password"},
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when missing one of phone, password and country code",
     *     403 = "Returned when authentication failure"
     *   }
     * )
     * @Rest\Post("/phone")
     * @Rest\View()
     *
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
            $regexConstraint = new Constraint\Regex(array('pattern' => '/[0-9\-]{2,5}/'));
            $regexConstraint->message = 'Invalid country code';
            
            if ($this->get('validator')->validateValue($country, $regexConstraint)->count())
            {
                $error = $this->get('translator')->trans('login.country.invalid');
                return new JsonResponse(array("error" => $error), Response::HTTP_BAD_REQUEST);
            }
            
            // verfiy phone
            $regexConstraint = new Constraint\Regex(array('pattern' => '/\d+/'));
            $regexConstraint->message = 'Invalid phone number';
            $regexConstraint->pattern = '/\d+/';
            
            if ($this->get('validator')->validateValue($phone, $regexConstraint)->count())
            {
                $error = $this->get('translator')->trans('login.mobile.number.invalid');
                return new JsonResponse(array("error" => $error), Response::HTTP_BAD_REQUEST);
            }
             
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle:User')
                       ->getUserByPhoneLogin($phone, $country);
            
            if (!$user instanceof User) {
                $error = $this->get('translator')->trans('login.bad.credential');
                return new JsonResponse(array("error" => $error), Response::HTTP_FORBIDDEN);
            }
            
            $encoder = $this->get('security.encoder_factory')
                            ->getEncoder($user);
            
            $encodedPass = $encoder->encodePassword($password, $user->getSalt());
            
            if($encodedPass != $user->getPassword()) {
                $error = $this->get('translator')->trans('login.bad.credential');
                return new JsonResponse(array("error" => $error), Response::HTTP_FORBIDDEN);
            }
        
            $user->updateToken();
            
            $em->persist($user);
            $em->flush();
            
            return array('apikey' => $user->getToken()->getKey());
        }
        else {
            $error = $this->get('translator')->trans('login.missing.parameter');
            return new JsonResponse(array("error" => $error), Response::HTTP_BAD_REQUEST);
        }
    }
    
    /**
     * Login with Facebook.
     *
     * @ApiDoc(
     *   resource = true,
     *   requirements = {
     *     {"name"="token", "dataType"="string", "requirement"="/[0-9a-zA-Z]+/", "required"=true, "description"="access token"},
     *   },
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

        $type = SocialType::FACEBOOK;
        
        $resData = array();
        
        if (!$token) 
        {
            $error = $this->get('translator')->trans('login.facebook.token.missing');
            return new JsonResponse(array("error" => $error), Response::HTTP_BAD_REQUEST);
        }
  
        $socialService = $this->container->get('social.service');
        
        try{
            //Verify token
            $result = $socialService->verifyFacebookToken($token);
    
            if (!isset($result->email)) {
                $error = $this->get('translator')->trans('login.facebook.email.missing');
                throw new AccessDeniedException($error);
            }
            
        }
        catch(AccessDeniedException $e) {
            return new JsonResponse(array("error" => $e->getMessage()), Response::HTTP_FORBIDDEN);
        }
        
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')
                   ->findOneBy(array('email' => $result->email));
        
        //TODO -- move to user model
        if ($user) {
            $socialAccount = $user->getSocialAccountByType($type);
            
            if ($socialAccount) {
                $socialAccount->setSmEmail($result->email);
                $socialAccount->setSmToken($token);
                $socialAccount->setSmName($result->name);
                $socialAccount->setSmId($result->id);

            }
            else {
                //TODO, is it possible?
                // It should not be possible in normal operation. 
                
            }
            
            // Should we update name to this social account name?
            $user->setName($result->name);
            $user->updateToken();

        }
        else {
            $user = new User();
            $user->setEmail($result->email);
            $user->setName($result->name);
            $user->generateUsername();
            $user->updateToken();
            
            $socialType = $em->getRepository('AppBundle:SocialType')
                             ->findOneBy(array('code' => $type));

            // Also add this social account to social login table
            $socialAccount = new SocialLogin();
            $socialAccount->setSmName($result->name);
            $socialAccount->setSmToken($token);
            $socialAccount->setSmEmail($result->email);
            $socialAccount->setSmId($result->id);
            $socialAccount->setType($socialType);
            $socialAccount->setCreated(new \DateTime('@' . time()));
            $socialAccount->setUser($user);
            $user->addSocialAccount($socialAccount);
            
            // First login to bind friendship automatically
            $friendIds = $socialService->getFacebookFriendIds($token);
            if (!empty($friendIds)) {
                $friendUserAccounts = $em->getRepository('AppBundle:User')
                                         ->fbAutoLinkUsers($friendIds);
                foreach($friendUserAccounts as $u)
                {
                    $user->addMyFriend($u);
                }
            }
            
            $resData['friend_count'] = isset($friendUserAccounts) ? count($friendUserAccounts) : 0;
        }
        
        // set avatar
        $picture = $socialService->getFacebookProfilePicture($result->id);

        if(!$media = $user->getAvatar()) {
            $media = new Media();
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $picture);
        $fileSize = filesize($picture);
        $fileName = 'fb_pic_' . $result->id;
        $uploadeFile = new UploadedFile(realpath($picture), $fileName, $mimeType, $fileSize, NULL, TRUE);
        
        $media->setFile($uploadeFile);
        $media->setUploadDir(User::AVATAR_UPLOAD_PATH . '/' . date('Y/m/d'));
        $user->setAvatar($media);
        $em->persist($user);
        $em->flush();

        $resData['apikey'] = $user->getToken()->getKey();
        return $resData;

    }
    
}

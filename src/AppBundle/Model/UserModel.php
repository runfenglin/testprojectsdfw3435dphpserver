<?php

namespace AppBundle\Model;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\DependencyInjection\Container;
use AppBundle\Exception\InvalidFormException;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use AppBundle\Entity AS Entity;

class UserModel extends AbstractModel
{
    public function __construct(Container $container)
    {   
        $this->_entity = new Entity\User();
        
        parent::__construct($container);
    }
    
    public function getLatestUpdate()
    {
        $updated = array();
        // For latest comments
        $updated['comment'] = $this->_getUpdatedComment($this->getEntity());
        
        return $updated;
    }
    
    protected function _getUpdatedComment(Entity\User $user)
    {
        $items = array();
        
        $em = $this->_container->get('doctrine')->getManager();
        $comments = $em->getRepository('AppBundle:Comment')
                      ->getUpdatedComment($user);
        
        if (count($comments)) {
            // changing last updating time
            $user->setUpdateAt(new \DateTime());
            $em->persist($user);
            $em->flush();
            
            foreach($comments as $k => $c) {
                $items[$k]['id'] = $c->getId();
                $items[$k]['parent'] = $c->getParent()->getId();
                $items[$k]['user'] = array(
                    'id' => $c->getUser()->getId(),
                    'name' => $c->getUser()->getName()
                );
                if ($c->getToUser()) {
                    $items[$k]['to_user'] = array(
                        'id' => $c->getToUser()->getId(),
                        'name' => $c->getToUser()->getName()
                    );
                }
                $items[$k]['content'] = $c->getComment();
            }
        }
        
        return $items;
    }
    
    public function post(array $parameters, $method = Request::METHOD_POST)
    {
        return $this->_processForm($this->_entity, $parameters, $method);
    }

    private function _processForm(Entity\User $user, array $parameters, $method = "PUT")
    {
        $form = $this->_container
                     ->get('form.factory')
                     ->create(
                        $this->_container->get('app.user.form.type'), 
                        $user, 
                        array('method' => $method)
                    );
        $form->submit($parameters, Request::METHOD_PUT !== $method);
        if ($form->isValid()) {

            $user = $form->getData();
            $em = $this->_container->get('doctrine')->getManager();
            
            $user->updateToken();
            $em->persist($user);
            $em->flush();
            
            return $user;
        }
        else{
            throw new InvalidFormException('Form validation failed', $form);
        }

        return FALSE;
    }
    
    public function expose($user = NULL)
    {
        $expose = array();
    
        if (NULL == $user) {
            $user = $this->_entity;
        } 
        
        if($user instanceof Entity\User && $user->getId()) {
            $user = array($user);
        }
        else if ($user instanceof Collection) {
            $user = $user->toArray();
        }
        else if(!is_array($user)) {
            return $expose;
        }
        
        foreach($user as $k => $u) {
        
            $socialAccounts = array();
            $friends = array();
            
            $socialAccounts['count'] = $u->getSocialAccounts()->count();
            $socialAccounts['data'] = array();
            
            foreach($u->getSocialAccounts() as $key => $account) {
                $social = array(
                    'type' => $account->getType()->getName(),
                    'sm_name' => $account->getSmName(),
                    'sm_email' => $account->getSmEmail(),
                    'sm_token' => $account->getSmToken(),
                    'created' => $account->getCreated()->getTimestamp(),
                ); 
                $socialAccounts['data'][] = $social;
            }
            
            $friends['count'] = $u->getMyFriends()->count() 
                                + $u->getFriendsWithMe()->count();
            $friends['data'] = array();
            
            foreach($u->getMyFriends() as $key => $friend) {
                $item = array(
                    'name' => $friend->getName(),
                    'username' => $friend->getUsername(),
                    'avatar' => $friend->base64EncodedAvatar(),
                    'created' => $friend->getCreated()->getTimestamp(),
                ); 
                $friends['data'][] = $item;
            }
            
            foreach($u->getFriendsWithMe() as $key => $friend) {
                $item = array(
                    'name' => $friend->getName(),
                    'username' => $friend->getUsername(),
                    'avatar' => $friend->base64EncodedAvatar(),
                    'created' => $friend->getCreated()->getTimestamp(),
                ); 
                $friends['data'][] = $item;
            }
            $data = array(
                'username' => $u->getUsername(),
                'name' => $u->getName(),
                'phone' => $u->getPhone(),
                'email' => $u->getEmail(),
                'avatar' => $u->base64EncodedAvatar(),
                'created' => $u->getCreated()->getTimestamp(),
                'socialAcccounts' => $socialAccounts,
                'friends' => $friends
            );
            
            $expose[] = $data;
        }
        
        return $expose;
    }
    
    public function likeActivity($id)
    {
        $user = $this->getEntity();
        
        
        $em = $this->_container->get('doctrine')->getManager();
        
        $activity = $em->getRepository('AppBundle:Activity')->find($id);
        
        // Only like activity, but not comment
        if ($activity
            && $user->getId()
            //    && $user->getId() != $activity->getUserId() 
                    && !$activity->getParent()) {
            
            $alreadyLike = $user->getLikes()->filter(function($e) use($id) {
                return $e->getId() == $id;
            });
            
            if (!$alreadyLike->count()){
                $user->addLike($activity);
            }
            $em->persist($user);
            $em->flush();
        }
        
        return array(
            'user_id' => $user->getId(),
            'activity_id' => $id,
            'count' => $activity->getLikeByUsers()->count() 
        );
    }
    
    public function facebookLogin($token)
    {
        $type = Entity\SocialType::FACEBOOK;
        
        $socialService = $this->_container->get('social.service');
        
        $result = $socialService->verifyFacebookToken($token);
    
        if (!isset($result->email)) {
            $error = $this->_container->get('translator')->trans('login.facebook.email.missing');
            throw new AccessDeniedException($error);
        }
        
        $em = $this->_container->get('doctrine')->getManager();
        $user = $em->getRepository('AppBundle:User')
                   ->findOneBy(array('email' => $result->email));
                   

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
            $user = new Entity\User();
            $user->setEmail($result->email);
            $user->setName($result->name);
            $user->generateUsername();
            $user->updateToken();
            
            $socialType = $em->getRepository('AppBundle:SocialType')
                             ->findOneBy(array('code' => $type));

            // Also add this social account to social login table
            $socialAccount = new Entity\SocialLogin();
            $socialAccount->setSmName($result->name);
            $socialAccount->setSmToken($token);
            $socialAccount->setSmEmail($result->email);
            $socialAccount->setSmId($result->id);
            $socialAccount->setType($socialType);
            $socialAccount->setCreated(new \DateTime());
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
            $media = new Entity\Media();
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $picture);
        $fileSize = filesize($picture);
        $fileName = 'fb_pic_' . $result->id;
        $uploadeFile = new UploadedFile(realpath($picture), $fileName, $mimeType, $fileSize, NULL, TRUE);
        
        $media->setFile($uploadeFile);
        $media->setUploadDir(Entity\User::AVATAR_UPLOAD_PATH . '/' . date('Y/m'));
        $user->setAvatar($media);
        $em->persist($user);
        $em->flush();

        $resData['apikey'] = $user->getToken()->getKey();
        return $resData;
    }
    
}   
    
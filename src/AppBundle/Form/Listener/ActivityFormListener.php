<?php
namespace AppBundle\Form\Listener;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\SecurityContextInterface;

// Entity
use AppBundle\Entity\Activity;
use AppBundle\Entity\Checkin;
use AppBundle\Entity\Comment;

// Constraints
use Symfony\Component\Validator\Constraints AS Constraint;

// FormError
use Symfony\Component\Form\FormError;

class ActivityFormListener implements EventSubscriberInterface
{
    private $_factory;
    
    private $_em;
    
    private $_security;
    
    private $_container;
    
    private $_activityEntity;
    
    public function __construct(FormFactoryInterface $factory, EntityManager $em, SecurityContextInterface $security, Container $container)
    {
        $this->_factory = $factory;
        $this->_em = $em;
        $this->_security = $security;
        $this->_container = $container;
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_BIND        => 'preBind',
            FormEvents::PRE_SET_DATA    => 'preSetData',
            FormEvents::POST_BIND       => 'postBind',
        );
    }
    
    public function preSetData(FormEvent $event)
    {   
        $this->_activityEntity = $event->getData();
			  
        $user = $this->_security->getToken()->getUser();
        if(!$this->_activityEntity->getUser() 
            || !$user->isEqualTo($this->_activityEntity->getUser())) {
            
            $this->_activityEntity->setUser($user);
            
            $event->setData($this->_activityEntity);
        }   

        $form = $event->getForm();
       
        $form->add(
            'parent', 
            'entity', 
            array(
                'class' => 'AppBundle:Activity',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('p');      
                },
                'required' => FALSE
            )
        );
        /*
        ->add(
            'medias', 
            'entity', 
            array(
                'class' => 'AppBundle:Media',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('m');      
                },
                'attr' => array(
                    'property' => 'fileName'
                ),
                'required' => FALSE
            )
        )
        */
        if ($this->_activityEntity instanceof Comment) {
            
            $form->add(
                'comment',
                'text',
                array(
                    'constraints' => array(
                        new Constraint\NotBlank(array('message'=>'comment is required'))
                    )
                )
            )->add(
                'toUser',
                'entity',
                array(
					'class' => 'AppBundle:User',
					'query_builder' => function(EntityRepository $er) {
						return $er->createQueryBuilder('u');      
					},
					'required' => FALSE
				)
            );
        }
        elseif ($this->_activityEntity instanceof Checkin) {
        
            $form->add(
                'checkinReference',
                'text',
                array(
                    'constraints' => array(
                        new Constraint\NotBlank(
                            array('message'=>'checkin reference is required')
                        ),
                        new Constraint\Regex(
                            array(
                                'pattern' => '/[a-z0-9\-\_]+/i',
                                'message' => 'Invalid checkin reference'
                            )
                        )
                    )
                )
            )->add(
                'checkinName',
                'text',
                array(
                    'constraints' => array(
                        new Constraint\NotBlank(
                            array('message'=>'checkin name is required')
                        )
                    )
                )
            )->add(
                'comment',
                'text',
                array(
                    'required' => FALSE
                )
            );
        }
        else {
            throw new \Exception('Invalid activity type to create');
        }
		
		
    }
    
    public function preBind(FormEvent $event)
    {  
		 
    }
    
    public function postBind(FormEvent $event) 
    {

    }

}

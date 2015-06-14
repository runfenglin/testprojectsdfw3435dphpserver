<?php
/**
 * User Form Listener
 * author: Haiping Lu
 */
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
use AppBundle\Entity\User;

// Constraints
use Symfony\Component\Validator\Constraints AS Constraint;

// FormError
use Symfony\Component\Form\FormError;

class UserFormListener implements EventSubscriberInterface
{
    private $_factory;
    
    private $_em;
    
    private $_security;
    
    private $_container;
    
    private $_userEntity;
    
    public function __construct(FormFactoryInterface $factory, EntityManager $em, SecurityContextInterface $security, Container $container)
    {
        $this->_factory = $factory;
        $this->_em = $em;
        $this->_security = $security;
        $this->_container = $container;
        $this->_translator = $container->get('translator');
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
        $this->_userEntity = $event->getData();
        
        if(!$this->_userEntity instanceof User || !$this->_userEntity->getId()) {
            
            $this->_userEntity = new User();
            $event->setData($this->_userEntity);
        }   
        
        $form = $event->getForm();
        
        $form->add(
            'name',
            'text',
            array(
                'constraints' => array(
                    new Constraint\NotBlank(array('message'=>$this->_translator->trans('register.name.required')))
                )
            )
        )->add(
            'username',
            'text',
            array(
                'constraints' => array(
                    new Constraint\NotBlank(array('message'=>$this->_translator->trans('register.username.required')))
                )
            )
        )->add(
            'phone',
            'text',
            array(
                'constraints' => array(
                    new Constraint\NotBlank(
                        array(
                            'message'=>$this->_translator->trans('register.mobile.number.required')
                        )
                    ),
                    new Constraint\Regex(
                        array(
                            'pattern' => '/\d+/',
                            'message' => $this->_translator->trans('register.mobile.number.invalid')
                        )
                    )
                )
            )
        )->add(
            'country',
            'number',
            array(
                'constraints' => array(
                    new Constraint\NotBlank(
                        array(
                            'message'=>$this->_translator->trans('register.country.required')
                        )
                    ),
                    new Constraint\Regex(
                        array(
                            'pattern' => '/[0-9\-]{2,5}/',
                            'message' => $this->_translator->trans('register.country.invalid')
                        )
                    )
                )
            )
        )->add(
            'avatar', 
            'entity', 
            array(
                'label' => 'Avatar',
                'class' => 'AppBundle:Media',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('m');      
                },
                'attr' => array(
                    'property' => 'fileName'
                ),
                'required' => FALSE
            )
        )->add(
            'password', 
            'repeated', 
            array(
                'required' => TRUE,
                'first_name' => 'password',
                'second_name' => 'confpass',
                'type' => 'password',
                'invalid_message' => $this->_translator->trans('register.password.dismatch'),
                'constraints' => array(
                    new Constraint\NotBlank(
                        array(
                            'message'=>$this->_translator->trans('register.password.required')
                        )
                    ), 
                    new Constraint\Regex(
                        array(
                            'pattern' => '/[0-9a-z\-\_]{6,20}/i',
                            'message' => $this->_translator->trans('register.password.invalid')
                        )
                    )
                ),
                'mapped' => FALSE
            )
        );
        
    }
    
    public function preBind(FormEvent $event)
    {
    }
    
    public function postBind(FormEvent $event) 
    {
        $password = $event->getForm()->get('password')->getData();
        
        $encoder = $this->_container->get('security.encoder_factory')
                        ->getEncoder($this->_userEntity);
            
        $encodedPass = $encoder->encodePassword($password, $this->_userEntity->getSalt());
        
        $this->_userEntity->setPassword($encodedPass);
            
    }

}

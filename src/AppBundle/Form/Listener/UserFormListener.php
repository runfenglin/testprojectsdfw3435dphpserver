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
                    new Constraint\NotBlank(array('message'=>'Please give a name'))
                )
            )
        )->add(
            'username',
            'text',
			array(
                'constraints' => array(
                    new Constraint\NotBlank(array('message'=>'Please give a username'))
                )
            )
        )->add(
            'phone',
            'number',
			array(
                'constraints' => array(
                    new Constraint\Regex(
						array(
							'pattern' => '/\d+/',
							'message' => 'Invalid phone number'
						)
					)
                )
            )
        )->add(
            'country',
            'text',
			array(
                'constraints' => array(
                    new Constraint\Country(
						array(
							'message' => 'Invalid country code'
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
        );
        
    }
    
    public function preBind(FormEvent $event)
    {
    }
    
    public function postBind(FormEvent $event) 
    {
        
    }

}

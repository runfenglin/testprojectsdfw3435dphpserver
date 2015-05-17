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
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;

// Entity
use AppBundle\Entity\Trip;

// Constraints
use Symfony\Component\Validator\Constraints AS Constraint;

// FormError
use Symfony\Component\Form\FormError;

class TripFormListener implements EventSubscriberInterface
{
    private $_factory;
    
    private $_em;
    
    private $_security;
    
    private $_container;
    
    private $_tripEntity;
    
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
        $this->_tripEntity = $event->getData();
        
        if(!$this->_tripEntity instanceof Trip || !$this->_tripEntity->getId()) {
            
            $this->_tripEntity = new Trip();
            $this->_tripEntity->setUser($this->_security->getToken()->getUser());
            $event->setData($this->_tripEntity);
        }   
        
        $form = $event->getForm();
        
        $form->add(
            'group',
            'checkbox'
        )->add(
            'departure',
            'text',
            array(
                'constraints' => array(
                    new Constraint\NotBlank(
                        array(
                            'message'=>$this->_translator->trans('trip.departure.required')
                        )
                    ),
                    new Constraint\Length(
                        array(
                            'min' => 3, 
                            'max' => 128, 
                            'maxMessage' => $this->_translator->trans(
                                'trip.departure.minlength.invalid'
                            ),
                            'maxMessage' => $this->_translator->trans(
                                'trip.departure.maxlength.invalid'
                            )
                        )
                    )
                )
            )
        )->add(
            'destination',
            'text',
            array(
                'constraints' => array(
                    new Constraint\NotBlank(
                        array(
                            'message'=>$this->_translator->trans('trip.destination.required')
                        )
                    ),
                    new Constraint\Length(
                        array(
                            'min'=> 3, 
                            'max'=> 128, 
                            'maxMessage' => $this->_translator->trans(
                                'trip.destination.minlength.invalid'
                            ),
                            'maxMessage' => $this->_translator->trans(
                                'trip.destination.maxlength.invalid'
                            )
                        )
                    )
                )
            )
        )->add(
            'time',
            'date',
            array(
                'input' => 'datetime',
                'widget' => 'single_text',
				'format' => 'yyyy-MM-dd HH:mm:ss',
				'invalid_message' => $this->_translator->trans('trip.time.invalid'),
                'data' => new \DateTime(),
                'constraints' => array(
                    new Constraint\NotBlank(
                        array(
                            'message' => $this->_translator->trans('trip.time.required')
                        )
                    ),
					new Constraint\GreaterThan(
                        array(
                            'message' => $this->_translator->trans('trip.time.passed'),
							'value' => date('Y-m-d H:i:s')
                        )
                    )
                )
            )
        )->add(
            'comment', 
            'text', 
            array(
                'required' => FALSE
            )
        )->add(
            'visibility', 
            'choice', 
            array(
                'choices' => array(
                    Trip::CIRCLE_FRIEND => 'Friends', 
                    Trip::CIRCLE_FRIEND_OF_FRIEND => 'Friends of Friend', 
                    Trip::CIRCLE_PUBLIC => 'Public'
                ),
				'invalid_message' => $this->_translator->trans('trip.visibility.invalid')
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

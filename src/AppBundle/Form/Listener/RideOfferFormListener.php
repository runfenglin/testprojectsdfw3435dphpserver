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
use AppBundle\Entity\RideOffer;

// Constraints
use Symfony\Component\Validator\Constraints AS Constraint;

// FormError
use Symfony\Component\Form\FormError;

class RideOfferFormListener implements EventSubscriberInterface
{
    private $_factory;
    
    private $_em;
    
    private $_security;
    
    private $_container;
    
    private $_rideOfferEntity;
    
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
        $this->_rideOfferEntity = $event->getData();
        
        if(!$this->_rideOfferEntity instanceof RideOffer || !$this->_rideOfferEntity->getId()) {
            
            $this->_rideOfferEntity = new RideOffer();
            $this->_rideOfferEntity->setUser($this->_security->getToken()->getUser());
            $event->setData($this->_rideOfferEntity);
        }   
        
        $form = $event->getForm();
        
        $form->add(
            'departure',
            'text',
            array(
                'required' => FALSE,
                'constraints' => array(

                    new Constraint\Length(
                        array(
                            'min' => 3, 
                            'max' => 128, 
                            'maxMessage' => $this->_translator->trans(
                                'rideOffer.departure.minlength.invalid'
                            ),
                            'maxMessage' => $this->_translator->trans(
                                'rideOffer.departure.maxlength.invalid'
                            )
                        )
                    )
                )
            )
        )->add(
            'destination',
            'text',
            array(
                'required' => FALSE,
                'constraints' => array(

                    new Constraint\Length(
                        array(
                            'min'=> 3, 
                            'max'=> 128, 
                            'maxMessage' => $this->_translator->trans(
                                'rideOffer.destination.minlength.invalid'
                            ),
                            'maxMessage' => $this->_translator->trans(
                                'rideOffer.destination.maxlength.invalid'
                            )
                        )
                    )
                )
            )
        )->add(
            'time',
            'date',
            array(
                'required' => FALSE,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'invalid_message' => $this->_translator->trans('rideOffer.time.invalid'),
                'data' => new \DateTime(),
                'constraints' => array(
                    new Constraint\GreaterThan(
                        array(
                            'message' => $this->_translator->trans('rideOffer.time.passed'),
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
            'trip', 
            'entity', 
            array(
                'class' => 'AppBundle:Trip',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('t');      
                },
                'attr' => array(
                    'property' => 'departure'
                ),
                'required' => TRUE
            )
        )->add(
            'departureReference',
            'text',
            array(
                'constraints' => array(

                    new Constraint\Regex(
                        array(
                            'pattern' => '/[a-z0-9\-\_]{3,255}/i',
                            'message' => $this->_translator->trans('rideOffer.departure.reference.invalid')
                        )
                    )
                )
            )
        )->add(
            'destinationReference',
            'text',
            array(
                'constraints' => array(

                    new Constraint\Regex(
                        array(
                            'pattern' => '/[a-z0-9\-\_]{3,255}/i',
                            'message' => $this->_translator->trans('rideOffer.destination.reference.invalid')
                        )
                    )
                )
            )
        );
        
    }
    
    public function preBind(FormEvent $event)
    {

    }
    
    public function postBind(FormEvent $event) 
    {
        $form = $event->getForm();
        
        $trip = $form->get('trip')->getData();
         
        if ($trip->getUser()->isEqualTo($this->_rideOfferEntity->getUser())) {
            $form['trip']->addError(
                new FormError(
                    $this->_container
                         ->get('translator')
                         ->trans('rideOffer.own.request')
                )
            );
        }
    }

}

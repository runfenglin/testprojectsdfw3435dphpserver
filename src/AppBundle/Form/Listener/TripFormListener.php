<?php
/**
 * Trip Form Listener
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
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;

// Entity
use AppBundle\Entity as Entity;

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
        
        if(!$this->_tripEntity instanceof Entity\Trip || !$this->_tripEntity->getId()) {
            
            $this->_tripEntity = new Entity\Trip();
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
                    Entity\Trip::CIRCLE_FRIEND => 'Friends', 
                    Entity\Trip::CIRCLE_FRIEND_OF_FRIEND => 'Friends of Friend', 
                ),
                'required' => FALSE,
                'invalid_message' => $this->_translator->trans('trip.visibility.invalid')
            )
        )->add(
            'departureReference',
            'text',
            array(
                'constraints' => array(
                    new Constraint\NotBlank(
                        array('message'=>$this->_translator->trans('trip.departure.reference.required'))
                    ),
                    new Constraint\Regex(
                        array(
                            'pattern' => '/[a-z0-9\-\_]{3,255}/i',
                            'message' => $this->_translator->trans('trip.departure.reference.invalid')
                        )
                    )
                )
            )
        )->add(
            'destinationReference',
            'text',
            array(
                'constraints' => array(
                    new Constraint\NotBlank(
                        array('message'=>$this->_translator->trans('trip.destination.reference.required'))
                    ),
                    new Constraint\Regex(
                        array(
                            'pattern' => '/[a-z0-9\-\_]{3,255}/i',
                            'message' => $this->_translator->trans('trip.destination.reference.invalid')
                        )
                    )
                )
            )
        )->add(
            'parent', 
            'entity', 
            array(
                'class' => 'AppBundle:Trip',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                              ->join('t.groupUsers', 'gu')
                              ->join('gu.user', 'u')
                              ->where('t.group = true')
                              ->andWhere('u = :User')
                              ->setParameter('User', $this->_security->getToken()->getUser());      
                },
                'invalid_message' => $this->_translator->trans('trip.create.parent.invalid'),
                'required' => FALSE
            )
        );
        
    }
    
    public function preBind(FormEvent $event)
    {
    //  var_dump($event->getData());die;
    }
    
    public function postBind(FormEvent $event) 
    {
        if ($parent = $event->getForm()->get('parent')->getData()) {

            $this->_tripEntity->setVisibility(Entity\Trip::CIRCLE_GROUP);
            $this->_tripEntity->setGroup(FALSE);            

        }
        else if ($event->getForm()->get('group')->getData()) {
            $groupUser = new Entity\GroupUser();
            
            $groupUser->setUser($this->_security->getToken()->getUser());
            $groupUser->setTrip($this->_tripEntity);
            $groupUser->setRole(Entity\GroupUser::ROLE_CREATOR);
            
            $this->_tripEntity->addGroupUser($groupUser);
        }
            
    }

}

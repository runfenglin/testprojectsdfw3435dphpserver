<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
//Listener
use AppBundle\Form\Listener\TripFormListener;

use Doctrine\Common\Collections\ArrayCollection;

class TripType extends AbstractType
{
    const FORM_NAME = 'triptype';
    
    private $_formListener;
    
    private $_container;
    
    public function __construct(TripFormListener $listener)
    {
        $this->_formListener = $listener;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber($this->_formListener);
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Trip',
            'csrf_protection' => FALSE
        ));
    }

    public function getName()
    {
        return self::FORM_NAME;
    }
}

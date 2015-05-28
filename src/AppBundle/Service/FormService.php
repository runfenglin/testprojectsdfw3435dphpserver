<?php
namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\Container;

class FormService
{
    private $_environment;
    
    private $_container;

    public function __construct($env, Container $container) {
        $this->_environment = $env;
        $this->_container = $container;
    }
    
    public function getErrorMessages(\Symfony\Component\Form\Form $form)
    {
        $errors = array();
        
        foreach ($form->getErrors() as $key => $error) {
            $template = $error->getMessageTemplate();
            $parameters = $error->getMessageParameters();

            foreach ($parameters as $var => $value) {
                $template = str_replace($var, $value, $template);
            }
            
            if (0 === strpos($template, '@')) {
                
                $template = $this->_container
                                 ->get('translator')
                                 ->trans(substr($template, 1));
            }
            $errors[$key] = $template;
        }
        if (count($form)) {
            foreach ($form as $name => $child) {
                if (!$child->isValid()) {
                    $errors[$name] = $this->getErrorMessages($child);
                }
            }
        }

        return $errors;
    }
}
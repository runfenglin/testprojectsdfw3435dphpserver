<?php
namespace AppBundle\Service;

class FormService
{
    private $_environment;

    public function __construct($env) {
        $this->_environment = $env;
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
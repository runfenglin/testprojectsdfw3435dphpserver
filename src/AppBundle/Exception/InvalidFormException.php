<?php
/**
 * Invalid Form Exception
 * author: Haiping Lu
 */
namespace AppBundle\Exception;

class InvalidFormException extends \RuntimeException
{
    protected $_form;

    public function __construct($message, $form = null)
    {
        parent::__construct($message);
        
        $this->_form = $form;
    }

    /**
     * @return array|null
     */
    public function getForm()
    {
        return $this->_form;
    }
}
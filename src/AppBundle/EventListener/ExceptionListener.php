<?php
namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionevent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\DependencyInjection\Container;

class ExceptionListener
{
    private $_container;

    public function __construct(Container $container)
    {
        $this->_container = $container;
    }
    public function onKernelException(GetResponseForExceptionevent $event)
    {
        $exception = $event->getException();
        
        if ($exception instanceof HttpException) {
        
            switch($exception->getStatusCode()) {
                case 404:
                case 405:{
                    
                    $response = new JsonResponse(array('error' => 'Invalid URL Address'), Response::HTTP_NOT_FOUND);                 
                    $event->setResponse($response);
                    break;
                }
                default:{
                    
                    break;
                }
            }
        }
        /*
        if($this->_container->get('security.context')->getToken()) {
        
            //email body building
            $body = $exception->getMessage() . PHP_EOL . nl2br($exception->getTraceAsString());
            //display more debugging/tracking information
            $body .= $this->_moreTrackingInfo();

            //send exception message to email
            $message = \Swift_Message::newInstance()
                       ->setSubject('Fusion Exception: ' . $email_subject)
                       ->setFrom($this->_default_from_email)
                       ->setTo($this->_default_from_email)
                       ->setBody($body, 'text/html');
            $this->_container->get('mailer')->send($message);
        }*/
    }
}
<?php
namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
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
				case 404: {
					
					$response = new Response('Invalid URL Address', Response::HTTP_NOT_FOUND);				 
					$event->setResponse($response);
					break;
				}
				default:{
					break;
				}
			}

		}
	}
}
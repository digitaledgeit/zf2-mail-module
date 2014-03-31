<?php

namespace DeitMailModule\Mail;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Service factory
 * @author James Newell <james@digitaledgeit.com.au>
 */
class ServiceFactory implements FactoryInterface {

	/**
	 * @inheritdoc
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {

		$renderer  = $serviceLocator->get('deit_mail_renderer');
		$transport = $serviceLocator->get('deit_mail_transport');

		$service = new Service();
		$service
			->setRenderer($renderer)
			->setTransport($transport)
		;

		return $service;
	}

}
 
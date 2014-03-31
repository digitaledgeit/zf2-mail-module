<?php

namespace DeitMailModule\Mail;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * View renderer factory
 * @author James Newell <james@digitaledgeit.com.au>
 */
class RendererFactory implements FactoryInterface {

	/**
	 * @inheritdoc
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {

		$config = $serviceLocator->get('Config');

		if (!isset($config['deit_mail']['renderer'])) {
			throw new \Exception("Mail renderer not configured.");
		}

		if (is_string($config['deit_mail']['renderer'])) {

			$renderer = $serviceLocator->get($config['deit_mail']['renderer']);

		} else {
			throw new \Exception("Mail renderer not configured.");
		}

		return $renderer;
	}

}
 
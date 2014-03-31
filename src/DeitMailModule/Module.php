<?php

namespace DeitMailModule;
use Zend\Mvc\MvcEvent;

/**
 * Module
 * @author James Newell <james@digitaledgeit.com.au>
 */
class Module {

	/**
	 * @inheritdoc
	 */
	public function getConfig() {
		return include __DIR__.'/../../config/module.config.php';
	}

	/**
	 * @inheritdoc
	 */
	public function getAutoloaderConfig() {
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__.'/src/'.__NAMESPACE__,
				),
			),
		);
	}

	/**
	 * @inheritdoc
	 */
	public function onBootstrap(MvcEvent $event) {
	}

}

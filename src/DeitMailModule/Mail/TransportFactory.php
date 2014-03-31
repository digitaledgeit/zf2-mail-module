<?php

namespace DeitMailModule\Mail;

use Zend\Mail\Transport\File;
use Zend\Mail\Transport\FileOptions;
use Zend\Mail\Transport\Null;
use Zend\Mail\Transport\Sendmail;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Mail transport factory
 * @author James Newell <james@digitaledgeit.com.au>
 */
class TransportFactory implements FactoryInterface {

	/**
	 * @inheritdoc
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {

		$config = $serviceLocator->get('Config');

		if (!isset($config['deit_mail']['transport'])) {
			throw new \Exception("Mail transport not configured.");
		}

		if (is_string($config['deit_mail']['transport'])) {

			$transport = $serviceLocator->get($config['deit_mail']['transport']);

		} else {

			if (!isset($config['deit_mail']['transport']['type'])) {
				throw new \Exception("Mail transport type not configured.");
			}

			$type       = $config['deit_mail']['transport']['type'];
			$options    = isset($config['deit_mail']['transport']['options']) ? $config['deit_mail']['transport']['options'] : [];

			switch ($config['deit_mail']['transport']['type']) {

				case 'null':
					$transport = new Null();
					break;

				case 'sendmail':
					$transport = new Sendmail($options);
					break;

				case 'smtp':
					$params     = new SmtpOptions($options);
					$transport  = new Smtp($params);
					break;

				case 'file':
					$params     = new FileOptions($options);
					$transport  = new File($params);
					break;

				default:
					throw new \Exception("Mail transport \"$type\" not supported.");

			}

		}

		return $transport;
	}

}
 
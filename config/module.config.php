<?php

namespace DeitMailModule;

return [
	'service_manager' => [
		'factories' => [
			'deit_mail_transport'   => 'DeitMailModule\Mail\TransportFactory',
			'deit_mail_renderer'    => 'DeitMailModule\Mail\RendererFactory',
			'deit_mail_service'     => 'DeitMailModule\Mail\ServiceFactory',


		],
		'invokables' => ['testtransport' => 'Zend\Mail\Transport\Sendmail']
	],
];

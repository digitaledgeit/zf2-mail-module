# ZF2 Mail Module

A module simplifying use of ZF2 Mail.

## Configuration

	'deit_mail' => [

		'transport' => [                    //an array containing type and options keys or a string containing a service name
			'type'      => '',              //null, file, sendmail or smtp
			'options'   => [                //see the transport options for selected type at http://framework.zend.com/manual/2.1/en/modules/zend.mail.introduction.html
			],
		],

		'renderer' => 'ViewRenderer'        //a string containing a service name
	],

## Sending mail

	//get the service
	$service = $serviceManager->get('deit_mail_service');

	//send a message containing plain text and HTML versions
	$service->sendMixedMessage(
		[
			'to'            => 'fred@example.com',
			'from'          => 'wilma@example.com',
			'subject'       => 'A test message from my app',
		],
		[
			'text/plain'    => 'email/hello-text',
			'text/html'     => 'email/hello-html'
		],
		[
			'name' => 'World!'
		]
	);
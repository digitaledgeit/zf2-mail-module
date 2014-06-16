# ZF2 Mail Module

A module simplifying use of ZF2 Mail.

## Installation

Add the module to your `composer.json` file and run `composer install`:

    "digitaledgeit/zf2-mail-module": "dev-master"

## Configuration

Add the module to the `modules` key in your `config/application.config.php` file:

	'modules' => [
		'DeitMailModule',
	],

Add the configuration to your `local.php` and `module.config.php`:

	'deit_mail' => [

		//an array containing type and options keys or a string containing a service name
		'transport' => [
			'type'      => '',              //null, file, sendmail or smtp
			'options'   => [                //see the transport options for selected type at http://framework.zend.com/manual/2.1/en/modules/zend.mail.introduction.html
			],
		],

		'renderer'  => 'ViewRenderer'        //a string containing a service name
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
			'attachments'   => [
                [
                    'type'      => 'text/html',
                    'name'      => 'test1.html',
                    'content'   => '<html><head><title>Test HTML Page</title></head><body><h1>Test HTML Page</h1></body></html>'
                ],
                [
                    'type'      => 'text/html',
                    'name'      => 'test2.html',
                    'content'   => './path/to/the-file.html'
                ]
            ]
		],
		[
			'text/plain'    => 'email/hello-text',
			'text/html'     => 'email/hello-html'
		],
		[
			'name' => 'World!'
		]
	);
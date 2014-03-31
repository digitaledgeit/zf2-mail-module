<?php

namespace DeitMailModule\Mail;
use Service as MailService;

/**
 * Service test
 * @author James Newell <james@digitaledgeit.com.au>
 */
class MailServiceTest extends \PHPUnit_Framework_TestCase {

	/**
	 * The service
	 * @var     MailService
	 */
	private $service;

	/**
	 * @inheritdoc
	 */
	protected function setUp() {
		$this->service = new MailService();
	}

	/**
	 * @inheritdoc
	 */
	protected function tearDown() {
		$this->service = null;
	}

	/**
	 * Tests the send
	 */
	public function testSendMessage() {

		$this->service->send([
			'to'        => 'info@pcosync.com',
			'from'      => 'jameslnewell@gmail.com',
			'subject'   => 'Test email',
		]);

	}

}
 
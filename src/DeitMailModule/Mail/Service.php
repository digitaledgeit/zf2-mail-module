<?php

namespace DeitMailModule\Mail;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mail\Message as Message;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Mime as Mime;
use Zend\Mime\Message as MimeMessage;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\RendererInterface;

/**
 * Mail service
 * @author James Newell <james@digitaledgeit.com.au>
 */
class Service {

	/**
	 * The mail transport
	 * @var     TransportInterface
	 */
	private $transport;

	/**
	 * The view renderer
	 * @var     RendererInterface
	 */
	private $renderer;

	/**
	 * Gets the mail transport
	 * @return  TransportInterface
	 * @throws
	 */
	public function getTransport() {

		if (is_null($this->transport)) {
			throw new \Exception('No mail transport set');
		}

		return $this->transport;
	}

	/**
	 * Sets the mail transport
	 * @param   TransportInterface $transport
	 * @return  $this
	 */
	public function setTransport(TransportInterface $transport) {
		$this->transport = $transport;
		return $this;
	}

	/**
	 * Gets the view renderer
	 * @return  RendererInterface
	 * @throws
	 */
	public function getRenderer() {

		if (is_null($this->transport)) {
			throw new \Exception('No mail transport set');
		}

		return $this->renderer;
	}

	/**
	 * Sets the view renderer
	 * @param   RendererInterface $renderer
	 * @return  $this
	 */
	public function setRenderer(RendererInterface $renderer) {
		$this->renderer = $renderer;
		return $this;
	}

	/**
	 * Renders a template
	 * @param   string  $template
	 * @param   mixed[] $variables
	 * @return  string
	 */
	public function renderTemplate($template, array $variables = []) {
		$viewModel = new ViewModel();
		$viewModel
			->setTemplate($template)
			->setVariables($variables)
		;
		return $this->getRenderer()->render($viewModel);
	}

	/**
	 * Creates a mail message
	 * @param   string[]    $params
	 * @return  Message
	 */
	public function createMessage(array $params) {
		$mailMessage = new Message;

		if (isset($params['to'])) {
			$mailMessage->addTo($params['to']);
		}

		if (isset($params['from'])) {
			$mailMessage->addFrom($params['from']);
		}

		if (isset($params['subject'])) {
			$mailMessage->setSubject($params['subject']);
		}

		return $mailMessage;
	}

	/**
	 * Sends a simple message via email
	 * @param   Message     $message
	 * @return  $this
	 */
	public function sendMessage(Message $message) {
		$this->getTransport()->send($message);
		return $this;
	}

	/**
	 * Sends a text message via email
	 * @param   string[]    $message
	 * @param   string      $template
	 * @param   string[]    $variables
	 * @return  $this
	 */
	public function sendTextMessage(array $message, $template, array $variables = [], $attachments=[]) {

		//render the template
		$viewContent = $this->renderTemplate($template, $variables);

		//create the message
		$mailMessage = $this->createMessage($message);
		$mimeMessage = new MimeMessage();
		$mimePart = new MimePart($viewContent);
		$mimePart->type = 'text/plain';
		$mimeMessage->addPart($mimePart);
		foreach ($attachments as $key =>$attachment) {
			$fileContents = fopen($attachment['file'], 'r');
			$mimeAttachment = new MimePart($fileContents);
			$mimeAttachment->type = $attachment['type'];
			$mimeAttachment->filename = $attachment['desiredName'];
			$mimeAttachment->disposition = Mime::DISPOSITION_ATTACHMENT;

			$mimeMessage->addPart($mimeAttachment);
		}
		$mailMessage
			->setBody($mimeMessage)
		;

		//send the message
		return $this->sendMessage($mailMessage);
	}

	/**
	 * Sends a HTML message via email
	 * @param   string[]    $message
	 * @param   string      $template
	 * @param   string[]    $variables
	 * @return  $this
	 */
	public function sendHtmlMessage(array $message, $template, array $variables = [], $attachments=[]) {

		//render the template
		$viewContent = $this->renderTemplate($template, $variables);

		//create the message
		$mailMessage = $this->createMessage($message);
		$mimeMessage = new MimeMessage();
		$mimePart = new MimePart($viewContent);
		$mimePart->type = 'text/html';
		$mimeMessage->addPart($mimePart);
		foreach ($attachments as $key =>$attachment) {
			$fileContents = fopen($attachment['file'], 'r');
			$mimeAttachment = new MimePart($fileContents);
			$mimeAttachment->type = $attachment['type'];
			$mimeAttachment->filename = $attachment['desiredName'];
			$mimeAttachment->disposition = Mime::DISPOSITION_ATTACHMENT;

			$mimeMessage->addPart($mimeAttachment);
		}
		$mailMessage
			->setBody($mimeMessage)
		;

		//send the message
		return $this->sendMessage($mailMessage);
	}

	/**
	 * Sends a mixed message via email
	 * @param   string[]    $message
	 * @param   string[]    $templates
	 * @param   string[]    $variables
	 * @return  $this
	 */
	public function sendMixedMessage(array $message, array $templates, array $variables = [], $attachments=[]) {

		//create the message
		$mailMessage = $this->createMessage($message);
		$mimeMessage = new MimeMessage();
		foreach ($templates as $mimeType => $template) {

			//render the template
			$viewContent = $this->renderTemplate($template, $variables);

			$mimePart = new MimePart($viewContent);
			$mimePart->type = $mimeType;
			$mimeMessage->addPart($mimePart);

		}
		$a = 0;
		foreach ($attachments as $key =>$attachment) {
			echo $a = $a+1;
			$fileContents = fopen($attachment['file'], 'r');
			$mimeAttachment = new MimePart($fileContents);
			$mimeAttachment->type = $attachment['type'];
			$mimeAttachment->filename = $attachment['desiredName'];
			$mimeAttachment->disposition = Mime::DISPOSITION_ATTACHMENT;

			$mimeMessage->addPart($mimeAttachment);
		}

		$mailMessage
			->setBody($mimeMessage)
			->getHeaders()->get('content-type')->setType('multipart/alternative') //let the client choose which part to display
		;

		//send the message
		return $this->sendMessage($mailMessage);
	}

}
 
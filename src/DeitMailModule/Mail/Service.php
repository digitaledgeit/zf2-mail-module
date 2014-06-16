<?php

namespace DeitMailModule\Mail;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mail\Message as MailMessage;
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
	 * @return  MailMessage
	 */
	public function createMessage(array $params) {
		$mailMessage = new MailMessage();
		$mimeMessage = new MimeMessage();

		if (isset($params['to'])) {
			$mailMessage->addTo($params['to']);
		}

		if (isset($params['from'])) {
			$mailMessage->addFrom($params['from']);
		}

		if (isset($params['subject'])) {
			$mailMessage->setSubject($params['subject']);
		}

		if (isset($params['attachments'])) {
			foreach ($params['attachments'] as $attachment) {

				if (is_file($attachment['content'])) {
					$content = fopen($attachment['content'], 'r');
				} else {
					$content = $attachment['content'];
				}

				//create the attachment
				$mimeAttachment = new MimePart($content);
				$mimeAttachment->type           = $attachment['type'];
				$mimeAttachment->filename       = $attachment['name'];
				$mimeAttachment->disposition    = Mime::DISPOSITION_ATTACHMENT;

				$mimeMessage->addPart($mimeAttachment);
			}
		}

		$mailMessage->setBody($mimeMessage);

		return $mailMessage;
	}

	/**
	 * Sends a simple message via email
	 * @param   MailMessage     $message
	 * @return  $this
	 */
	public function sendMessage(MailMessage $message) {
		$this->getTransport()->send($message);
		return $this;
	}

	/**
	 * Sends a mixed message via email
	 * @param   string[]    $message
	 * @param   string[]    $templates
	 * @param   string[]    $variables
	 * @return  $this
	 */
	public function sendMixedMessage(array $message, array $templates, array $variables = []) {

		//create the message
		$mailMessage = $this->createMessage($message);

		//render the templates
		$contentMimeMessage = new MimeMessage();
		foreach ($templates as $mimeType => $template) {

			//render the template
			$viewContent = $this->renderTemplate($template, $variables);

			//add the template to the message
			$mimePart = new MimePart($viewContent);
			$mimePart->type = $mimeType;
			$contentMimeMessage->addPart($mimePart);

		}

		//combine the alternative content into a single mime part
		if ($contentMimeMessage->isMultiPart()) {
			$contentMimePart        = new MimePart($contentMimeMessage->generateMessage());
			$contentMimePart->type  = 'multipart/alternative;'.PHP_EOL.' boundary="'.$contentMimeMessage->getMime()->boundary().'"';
			$contentMimeParts       = [$contentMimePart];
		} else {
			$contentMimeParts = $contentMimeMessage->getParts();
		}

		//order the content before any attachments
		$finalMimeMessage = new MimeMessage();
		$finalMimeMessage->setParts(array_merge(
			$contentMimeParts,
			$mailMessage->getBody()->getParts()
		));
		$mailMessage->setBody($finalMimeMessage);

		//let the client choose which part to display
		if ($mailMessage->getBody()->isMultiPart()) {
			$mailMessage
				->getHeaders()->get('content-type')->setType('multipart/mixed')
			;
		}

		//send the message
		return $this->sendMessage($mailMessage);
	}

	/**
	 * Sends a text message via email
	 * @param   string[]    $message
	 * @param   string      $template
	 * @param   string[]    $variables
	 * @return  $this
	 */
	public function sendTextMessage(array $message, $template, array $variables = []) {
		return $this->sendMixedMessage($message,['text/plain' => $template], $variables);
	}

	/**
	 * Sends a HTML message via email
	 * @param   string[]    $message
	 * @param   string      $template
	 * @param   string[]    $variables
	 * @return  $this
	 */
	public function sendHtmlMessage(array $message, $template, array $variables = []) {
		return $this->sendMixedMessage($message,['text/html' => $template], $variables);
	}

}
 
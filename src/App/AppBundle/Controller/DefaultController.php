<?php

namespace App\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
	public function sendTestEmailAction()
	{
		$dodałemZmiennaIdupa = 1;
		$to = 'powiadomienia@wgn.pl';
		// Create the message
		$message = \Swift_Message::newInstance()
			->setSubject('Test Email')
			->setFrom('powiadomienia@wgn.pl')
			->setTo($to)
			->setBody(
				$this->renderView(
					'AppOfficeBundle:Emails:test.html.twig'
				),
				'text/html'
			);

		// Send the message
		$this->get('mailer')->send($message);
		var_dump(123);

		return new Response('Test email sent successfully!');
	}
}
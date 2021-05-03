<?php

namespace Maurit\Bundle\SmsBundle\Provider;


use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Maurit\Bundle\SmsBundle\Exception\SmsDiscountException;
use Maurit\Bundle\SmsBundle\Sms\SmsInterface;

class SmsDiscountProvider
	implements ProviderInterface
{
	private const SMS_SEND_URI = 'http://api.iqsms.ru/messages/v2/send/';

	/** @var string */
	private $login;
	/** @var string */
	private $password;
	/** @var null|string */
	private $sender;
	/** @var bool */
	private $flash;
	/** @var ClientInterface */
	private $client;


	public function __construct()
	{
		$this->setClient(new Client);
	}

	public function setClient(ClientInterface $client): self
	{
		$this->client = $client;

		return $this;
	}

	public function setLogin(string $login): self
	{
		$this->login = $login;

		return $this;
	}

	public function setPassword(string $password): self
	{
		$this->password = $password;

		return $this;
	}

	public function setSender(?string $sender): self
	{
		$this->sender = $sender;

		return $this;
	}

	public function setFlash(bool $flash): self
	{
		$this->flash = $flash;

		return $this;
	}

	public function send(SmsInterface $sms)
	{
		$response = $this->client->request('POST', self::SMS_SEND_URI, $this->getPostData($sms));
		$responseData = explode(';', $response->getBody()->getContents());

		if ($responseData[0] != 'accepted') {
			throw new SmsDiscountException($responseData[1]);
		}

		return true;
	}

	private function getPostData(SmsInterface $sms): array
	{
		$post = [
			'auth' => [
				$this->login,
				$this->password
			],
			'form_params' => [
				'phone' => $sms->getPhoneNumber(),
				'text' => $sms->getMessage(),
				'scheduleTime' => $sms->getDateTime()->format(\DateTime::RFC3339)
			]
		];

		if ($this->sender) {
			$post['form_params']['sender'] = $this->sender;
		}

		if ($this->flash) {
			$post['form_params']['flash'] = 1;
		}

		return $post;
	}
}

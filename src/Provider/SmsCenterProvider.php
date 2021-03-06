<?php

namespace Maurit\Bundle\SmsBundle\Provider;


use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Maurit\Bundle\SmsBundle\Exception\SmsCenterException;
use Maurit\Bundle\SmsBundle\Sms\SmsInterface;


class SmsCenterProvider
	implements ProviderInterface
{
	private const SMS_SEND_URI = 'https://smsc.ru/sys/send.php';
	private const STATUS_URI = 'https://smsc.ru/sys/status.php';
	private const BALANCE_URI = 'https://smsc.ru/sys/balance.php';

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

	public function setPassword(?string $password): self
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

	public function send(SmsInterface $sms): bool
	{
		$response = $this->client->request('POST', self::SMS_SEND_URI, $this->getPostData($sms));
		$jsonResponse = json_decode($response->getBody()->getContents());

		if (property_exists($jsonResponse, 'error_code')) {
			throw new SmsCenterException($jsonResponse->error_code);
		}

		return true;
	}

	private function getPostData(SmsInterface $sms): array
	{
		$post = [
			'form_params' => [
				'login' => $this->login,
				'psw' => $this->getPassword(),
				'phones' => $sms->getPhoneNumber(),
				'mes' => $sms->getMessage(),
				'time' => $this->getTime($sms->getDateTime()),
				'flash' => (int)$this->flash,
				'fmt' => 3, // Get response in json format
				'charset' => 'utf-8', // Use unicode charset in message text
			]
		];

		if ($this->sender) {
			$post['form_params']['sender'] = $this->sender;
		}

		return $post;
	}

	private function getPassword(): ?string
	{
		return $this->password;
	}

	private function getTime(\DateTime $dateTime): string
	{
		// Zero at returned string mean that we send timestamp time format
		return sprintf('0%s', $dateTime->getTimestamp());
	}

	public function balance(): float
	{
		$response = $this->client->request('POST', self::BALANCE_URI, $this->getPostBalanceData());
		$jsonResponse = json_decode($response->getBody()->getContents());

		if (property_exists($jsonResponse, 'error_code')) {
			throw new SmsCenterException($jsonResponse->error_code);
		}

		return (float)$jsonResponse->balance;
	}

	private function getPostBalanceData(): array
	{
		return [
			'form_params' => [
				'login' => $this->login,
				'psw' => $this->getPassword(),
				'fmt' => 3, // Get response in json format
				'cur' => 0
			]
		];
	}

	public function check($id): string
	{
		$response = $this->client->request('POST', self::STATUS_URI, $this->getPostCheckData());
		$jsonResponse = json_decode($response->getBody()->getContents());

		if (property_exists($jsonResponse, 'error_code')) {
			throw new SmsCenterException($jsonResponse->error_code);
		}

		return $jsonResponse->status;
	}

	private function getPostCheckData(): array
	{
		return [
			'form_params' => [
				'login' => $this->login,
				'psw' => $this->getPassword(),
				'fmt' => 3, // Get response in json format
				'charset' => 'utf-8', // Use unicode charset in message text
			]
		];
	}
}

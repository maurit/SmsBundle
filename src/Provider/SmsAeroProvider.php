<?php

namespace Maurit\Bundle\SmsBundle\Provider;


use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Maurit\Bundle\SmsBundle\Exception\SmsAeroException;
use Maurit\Bundle\SmsBundle\Sms\SmsInterface;


class SmsAeroProvider
	implements ProviderInterface
{
	private const SMS_SEND_URI = 'https://gate.smsaero.ru/v2/sms/send';
	private const SMS_STATUS_URI = 'https://gate.smsaero.ru/v2/sms/status';
	private const BALANCE_URI = 'https://gate.smsaero.ru/v2/balance';

	/** @var string */
	private $user;
	/** @var string */
	private $apiKey;
	/** @var string */
	private $sign;
	/** @var string */
	private $channel;
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

	public function setUser(string $user): self
	{
		$this->user = $user;

		return $this;
	}

	public function setApiKey(string $apiKey): self
	{
		$this->apiKey = $apiKey;

		return $this;
	}

	public function setSign(string $sign): self
	{
		$this->sign = $sign;

		return $this;
	}

	public function setChannel(string $channel): self
	{
		$this->channel = $channel;

		return $this;
	}

	public function send(SmsInterface $sms): bool
	{
		$response = $this->client->request('POST', self::SMS_SEND_URI, $this->getPostSendData($sms));
		$jsonResponse = json_decode($response->getBody()->getContents());

		if (!$jsonResponse->success === true) {
			throw new SmsAeroException(json_encode($jsonResponse));
		}

		return true;
	}

	private function getPostSendData(SmsInterface $sms): array
	{
		return [
			'headers' => [
				'accept' => 'application/json'
			],
			'auth' => [
				$this->user,
				$this->apiKey
			],
			'form_params' => [
				'sign' => $this->sign,
				'channel' => $this->channel,
				'number' => $sms->getPhoneNumber(),
				'text' => $sms->getMessage(),
				'dateSend' => $sms->getDateTime()->getTimestamp(),
			]
		];
	}

	public function balance(): float
	{
		$response = $this->client->request('POST', self::BALANCE_URI, $this->getPostBalanceData());
		$jsonResponse = json_decode($response->getBody()->getContents());

		if (!$jsonResponse->success === true) {
			throw new SmsAeroException(json_encode($jsonResponse));
		}

		return (float)$jsonResponse->data->balance;
	}

	private function getPostBalanceData(): array
	{
		return [
			'headers' => [
				'accept' => 'application/json'
			],
			'auth' => [
				$this->user,
				$this->apiKey
			]
		];
	}

	public function check($id): string
	{
		$response = $this->client->request('POST', self::SMS_STATUS_URI, $this->getPostStatusData($id));
		$jsonResponse = json_decode($response->getBody()->getContents());

		if ($jsonResponse->success !== true) {
			throw new SmsAeroException(json_encode($jsonResponse));
		}

		return $jsonResponse->data->extendStatus;
	}

	private function getPostStatusData($id): array
	{
		return [
			'headers' => [
				'accept' => 'application/json'
			],
			'auth' => [
				$this->user,
				$this->apiKey
			],
			'form_params' => [
				'id' => $id
			]
		];
	}
}

<?php

namespace Maurit\Bundle\SmsBundle\Provider;


use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Maurit\Bundle\SmsBundle\Exception\SmsRuException;
use Maurit\Bundle\SmsBundle\Sms\SmsInterface;


class SmsRuProvider
	implements ProviderInterface
{
	private const SMS_SEND_URI = 'https://sms.ru/sms/send';
	private const SMS_STATUS_URI = 'https://sms.ru/sms/status';
	private const BALANCE_URI = 'https://sms.ru/my/balance';

	/** @var string */
	private $apiId;
	/** @var string */
	private $from;
	/** @var bool */
	private $test;
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

	public function setApiId(string $apiId): self
	{
		$this->apiId = $apiId;

		return $this;
	}

	public function setFrom(string $from): self
	{
		$this->from = $from;

		return $this;
	}

	public function setTest(bool $test): self
	{
		$this->test = $test;

		return $this;
	}

	public function send(SmsInterface $sms): bool
	{
		$response = $this->client->request('POST', self::SMS_SEND_URI, $this->getPostData($sms));
		$responseCode = (int)$response->getBody()->read(3);

		if ($responseCode != 100) {
			throw new SmsRuException($responseCode);
		}

		return true;
	}

	private function getPostData(SmsInterface $sms): array
	{
		return [
			'form_params' => [
				'api_id' => $this->apiId,
				'from' => $this->from,
				'to' => $sms->getPhoneNumber(),
				'msg' => $sms->getMessage(),
				'time' => $sms->getDateTime()->getTimestamp(),
				'test' => (int)$this->test,
			],
		];
	}

	public function balance(): float
	{
		$response = $this->client->request('POST', self::BALANCE_URI, $this->getPostBalanceData());
		$jsonResponse = json_decode($response->getBody()->getContents());
		$responseCode = (int)$jsonResponse->status_code;

		if ($responseCode != 100) {
			throw new SmsRuException($responseCode);
		}

		return (float)$jsonResponse->balance;
	}

	private function getPostBalanceData(): array
	{
		return [
			'form_params' => [
				'api_id' => $this->apiId
			]
		];
	}

	public function check($id): string
	{
		$response = $this->client->request('POST', self::SMS_STATUS_URI, $this->getPostStatusData($id));
		$jsonResponse = json_decode($response->getBody()->getContents(), true);
		$responseCode = (int)$jsonResponse['status_code'];

		if ($responseCode != 100) {
			throw new SmsRuException($responseCode);
		}

		return $jsonResponse['sms'][$id]['status_text'];
	}

	private function getPostStatusData($id): array
	{
		return [
			'form_params' => [
				'api_id' => $this->apiId,
				'sms_id' => $id
			]
		];
	}
}

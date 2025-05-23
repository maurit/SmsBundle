<?php declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Maurit\Bundle\SmsBundle\Exception\SmsRuException;
use Maurit\Bundle\SmsBundle\Sms\SmsInterface;
use Nette\Utils\Json;


class SmsRuProvider
	implements ProviderInterface
{
	private const SMS_SEND_URI = 'https://sms.ru/sms/send';
	private const SMS_STATUS_URI = 'https://sms.ru/sms/status';
	private const BALANCE_URI = 'https://sms.ru/my/balance';

	private string $apiId = '';
	private string $from = '';
	private bool $test = false;
	private ClientInterface $client;


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

	public function setTest(bool $test = true): self
	{
		$this->test = $test;
		return $this;
	}

	public function send(SmsInterface $sms): string
	{
		$respRaw = $this->client->request('POST', self::SMS_SEND_URI, $this->getPostData($sms))->getBody()->getContents();
		$respJson = Json::decode($respRaw, Json::FORCE_ARRAY);
		if ($respJson['status'] !== 'OK') {
			throw new SmsRuException($respJson['status_code']);
		}

		return (string)array_key_first($respJson['sms']);
	}

	private function getPostData(SmsInterface $sms): array
	{
		return [
			RequestOptions::FORM_PARAMS => [
				'api_id' => $this->apiId,
				'from' => $this->from,
				'to' => $sms->getPhoneNumber(),
				'msg' => $sms->getMessage(),
				'time' => $sms->getDateTime()->getTimestamp(),
				'test' => (int)$this->test,
				'json' => 1
			],
		];
	}

	public function balance(): float
	{
		$response = $this->client->request('POST', self::BALANCE_URI, $this->getPostBalanceData());
		$jsonResponse = json_decode($response->getBody()->getContents());
		$responseCode = (int)$jsonResponse->status_code;

		if ($responseCode !== 100) {
			throw new SmsRuException($responseCode);
		}

		return (float)$jsonResponse->balance;
	}

	private function getPostBalanceData(): array
	{
		return [
			RequestOptions::FORM_PARAMS => [
				'api_id' => $this->apiId
			]
		];
	}

	public function check($id): string
	{
		$response = $this->client->request('POST', self::SMS_STATUS_URI, $this->getPostStatusData($id));
		$jsonResponse = json_decode($response->getBody()->getContents(), true);
		$responseCode = (int)$jsonResponse['status_code'];

		if ($responseCode !== 100) {
			throw new SmsRuException($responseCode);
		}

		return $jsonResponse['sms'][$id]['status_text'];
	}

	private function getPostStatusData($id): array
	{
		return [
			RequestOptions::FORM_PARAMS => [
				'api_id' => $this->apiId,
				'sms_id' => $id
			]
		];
	}
}

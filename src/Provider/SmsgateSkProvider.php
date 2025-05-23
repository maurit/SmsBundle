<?php declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Maurit\Bundle\SmsBundle\Exception\SmsgateSkException;
use Maurit\Bundle\SmsBundle\Sms\SmsInterface;
use Nette\Utils\Strings;


class SmsgateSkProvider
	implements ProviderInterface
{
	private const API_JSON_URI = 'https://api.smsgate.sk/json';
//	private const API_XML_URI = 'https://api.smsgate.sk/xml';
//	private const API_SOAP_URI = 'https://api.smsgate.sk/soap';

	private string $token = '';
	private ClientInterface $client;
	private bool $textNumbers = false;
	private bool $unicode;


	public function __construct()
	{
		$this->setClient(new Client);
	}

	public function setClient(ClientInterface $client): self
	{
		$this->client = $client;
		return $this;
	}

	public function setToken(string $token): self
	{
		$this->token = $token;
		return $this;
	}

	public function setTextNumbers(bool $textNumbers = true): self
	{
		$this->textNumbers = $textNumbers;
		return $this;
	}

	public function setUnicode(bool $unicode): self
	{
		$this->unicode = $unicode;
		return $this;
	}

	public function send(SmsInterface $sms): int
	{
		try {
			$respRaw = $this->client->request('POST', self::API_JSON_URI . '/send_message', $this->getPostSendData($sms))->getBody()->getContents();
			$respJson = json_decode($respRaw, true);
		} catch (\Exception $e) {
			throw new SmsgateSkException($e->getMessage());
		}
		if ($respJson['result']['code'] !== 'OK' || $respJson['messages'][0]['code'] !== 'OK') {
			throw new SmsgateSkException($respJson->result->code);
		}
		return $respJson['messages'][0]['message_id'];
	}

	public function balance(): float
	{
		return 0.0;
	}

	public function check($id): string
	{
		try {
			$respRaw = $this->client->request('POST', self::API_JSON_URI . '/check_message', $this->getPostCheckData($id))->getBody()->getContents();
			$respJson = json_decode($respRaw);
		} catch (\Exception $e) {
			throw new SmsgateSkException($e->getMessage());
		}
		if ($respJson->result->status === 'success') {
			return $respJson->code;
		}
		throw new SmsgateSkException($respJson->result->code);
	}

	private function getPostSendData(SmsInterface $sms): array
	{
		if (($sender = $sms->getSender()) === null || $sender === '') {
			throw new SmsgateSkException('Sender was not set');
		}

		try {
			$pnu = PhoneNumberUtil::getInstance();
			$rcptS = Strings::replace($pnu->format($pnu->parse($sms->getPhoneNumber(), 'SK'), PhoneNumberFormat::INTERNATIONAL), '% %', '');
		} catch (\Throwable $ex) {
			throw new SmsgateSkException('invalid recipient number');
		}

		$isAscii = $sms->getMessage() === Strings::toAscii($sms->getMessage());
		if (!$isAscii && !$this->unicode) {
			throw new SmsgateSkException('only ASCII text allowed');
		}

		$data = [
			RequestOptions::HEADERS => [
				'accept' => 'application/json'
			],
			RequestOptions::JSON => [
				'token' => $this->token,
				'to' => $rcptS,
				'text' => $sms->getMessage(),
				'concat' => 3,
				'unicode' => !$isAscii,
				'delivery_time' => $sms->getDateTime()->format('Y-m-d H:i:s')
			]
		];
		if ($this->textNumbers) {
			$data[RequestOptions::JSON]['from'] = $sms->getSender() ?? '';
		}
		return $data;
	}

	private function getPostCheckData($id): array
	{
		if ('' === (string)$id) {
			throw new SmsgateSkException('Id was not set');
		}

		return [
			RequestOptions::HEADERS => [
				'accept' => 'application/json'
			],
			RequestOptions::JSON => [
				'token' => $this->token,
				'message_id' => $id
			]
		];
	}
}

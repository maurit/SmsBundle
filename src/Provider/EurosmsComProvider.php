<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Provider;


use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Maurit\Bundle\SmsBundle\Exception\EurosmsComException;
use Maurit\Bundle\SmsBundle\Sms\SmsInterface;
use Nette\Utils\Strings;


class EurosmsComProvider
	implements ProviderInterface
{
	private const SMS_SEND_URI = 'https://as.eurosms.com/api/v3/send/one';
	private const SMS_SEND_URI_TEST = 'http://as.eurosms.com/api/v3/test/one';
	private const SMS_STATUS_URI = 'http://as.eurosms.com/api/v3/status/one/';
	private const BALANCE_URI = 'https://as.eurosms.com/api/v2/balance';
	private const SMS_STATUS1_URI = 'http://as.eurosms.com/sms/Sender';

	/** @var string */
	private $key;
	/** @var string */
	private $id;
	/** @var ClientInterface */
	private $client;
	/** @var bool */
	private $test;
	/** @var bool */
	private $unicode;
	/** @var bool */
	private $long;


	public function __construct()
	{
		$this->setClient(new Client);
	}

	public function setClient(ClientInterface $client): self
	{
		$this->client = $client;
		return $this;
	}

	public function setId(string $id): self
	{
		$this->id = $id;
		return $this;
	}

	public function setKey(string $key): self
	{
		$this->key = $key;
		return $this;
	}

	public function setTest(bool $test): self
	{
		$this->test = $test;
		return $this;
	}

	public function setUnicode(bool $unicode): self
	{
		$this->unicode = $unicode;
		return $this;
	}

	public function setLong(bool $long): self
	{
		$this->long = $long;
		return $this;
	}

	public function send(SmsInterface $sms): string
	{
		$respRaw = $this->client->request('POST', $this->test ? self::SMS_SEND_URI_TEST : self::SMS_SEND_URI, $this->getPostSendData($sms))->getBody()->getContents();
		$respJson = json_decode($respRaw, true);

		if (!isset($respJson['uuid'])) {
			throw new EurosmsComException($respRaw);
		}

		return trim($respJson['uuid'][0]);
	}

	private function getPostSendData(SmsInterface $sms): array
	{
		if (empty($sms->getSender())) {
			throw new EurosmsComException('Sender was not set');
		}

		try {
			$pnu = PhoneNumberUtil::getInstance();
			$parsed = $pnu->parse($sms->getPhoneNumber(), 'SK');
			$rcptN = (int)Strings::trim(Strings::replace($pnu->format($parsed, PhoneNumberFormat::INTERNATIONAL), '% %', ''), '+');
		} catch (\Throwable $ex) {
			throw new EurosmsComException('invalid recipient number');
		}

		$isAscii = $sms->getMessage() === Strings::toAscii($sms->getMessage());
		if (!$isAscii && !$this->unicode) {
			throw new EurosmsComException('only ASCII text allowed');
		}
		$isLong = Strings::length($sms->getMessage()) > ($isAscii ? 160 : 70);
		if ($isLong && !$this->long) {
			throw new EurosmsComException('text too long');
		}

		$data = [
			'headers' => [
				'accept' => 'application/json'
			],
			'json' => [
				'iid' => $this->id,
				'sgn' => $this->calcSignature($sms->getSender(), $rcptN, $sms->getMessage()),
				'rcpt' => $rcptN,
				'flgs' => 0x01 /*Require delivery report*/ | ($isLong ? 0x02 : 0) | ($isAscii ? 0 : 0x04),
				'sndr' => $sms->getSender(),
				'txt' => $sms->getMessage(),
			]
		];
		if ($sms->getDateTime()) {
			$data['json']['sch'] = $sms->getDateTime()->format('Y-m-d H:i');
		}
		return $data;
	}

	private function calcSignature($sender, $rcpt, $msg): string
	{
		return hash_hmac('sha256', $sender . $rcpt . $msg, $this->key);
	}

	public function balance(): float
	{
		try {
			$respRaw = $this->client->request('GET', self::BALANCE_URI, $this->getGetBalanceData())->getBody()->getContents();
		} catch (\Exception $e) {
			throw new EurosmsComException($e->getMessage());
		}
		if (strpos($respRaw, 'BAD_INT') !== false) {
			throw new EurosmsComException('Bad integration key');
		}
		return (float)$respRaw;
	}

	private function getGetBalanceData(): array
	{
		return [
			'query' => [
				'i' => $this->id
			]
		];
	}

	public function check($id): string
	{
		if (Strings::match((string)$id, '/^\d+$/')) {
			return $this->check1($id);
		}
		if (Strings::match($id, '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/')) {
			return $this->check3($id);
		}
		throw new EurosmsComException('invalid ID: ' . $id);
	}

	private function check1($id): string
	{
		try {
			$respRaw = $this->client->request('GET', self::SMS_STATUS1_URI, $this->getGetCheck1Data($id))->getBody()->getContents();
			$resp = explode('|', $respRaw);

			if ($resp[0] != 'ok') {
				throw new EurosmsComException($respRaw);
			}

			return trim($resp[1]);
		} catch (\Exception $e) {
			throw new EurosmsComException($e->getMessage() . "\n" . $respRaw);
		}
	}

	private function getGetCheck1Data($id): array
	{
		return [
			'query' => [
				'action' => 'status1SMSHTTP',
				'i' => $id
			]
		];
	}

	private function check3($id): string
	{
		try {
			$respRaw = $this->client->request('GET', self::SMS_STATUS_URI . $id, $this->getGetCheck3Data())->getBody()->getContents();
			$respJson = json_decode($respRaw);
		} catch (\Exception $e) {
			throw new EurosmsComException($e->getMessage() . "\n" . $respRaw);
		}
		if ($respJson->err_code == 'OK') {
			switch ($respJson->dlr) {
				case 'ENROUTE':
					return 'Queued';
				case 'ACCEPTD':
					return 'Accepted';
				case 'DELIVRD':
					return 'Delivered';
				case 'UNDELIV':
					return 'Undelivered';
				case 'EXPIRED':
					return 'Expired';
				case 'REJECTD':
					return 'Rejected';
				case 'DELETED':
					return 'Cancelled';
				case 'UNKNOWN':
					return 'Unknown';
				case 'ERR_NO_SUCH_UUID':
				default:
					return '?' . $respJson->dlr;
			}
		}
		throw new EurosmsComException($respRaw);
	}

	private function getGetCheck3Data(): array
	{
		return [
			'headers' => [
				'accept' => 'application/json'
			]
		];
	}
}

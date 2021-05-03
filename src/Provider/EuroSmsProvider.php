<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Provider;


use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Maurit\Bundle\SmsBundle\Exception\EuroSmsException;
use Maurit\Bundle\SmsBundle\Sms\SmsInterface;
use Nette\Utils\Strings;


class EuroSmsProvider
	implements ProviderInterface
{
	private const SMS_SEND_URI = 'https://as.eurosms.com/api/v3/send/one';
	private const SMS_SEND_URI_TEST = 'http://as.eurosms.com/api/v3/test/one';

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
			throw new EuroSmsException($respRaw);
		}

		return trim($respJson['uuid'][0]);
	}

	private function getPostSendData(SmsInterface $sms): array
	{
		if (empty($sms->getSender())) {
			throw new EuroSmsException('Sender was not set');
		}

		try {
			$pnu = PhoneNumberUtil::getInstance();
			$parsed = $pnu->parse($sms->getPhoneNumber(), 'SK');
			$rcptN = (int)Strings::trim(Strings::replace($pnu->format($parsed, PhoneNumberFormat::INTERNATIONAL),'% %', ''), '+');
		} catch (\Throwable $ex) {
			throw new EuroSmsException('invalid recipient number');
		}

		$isAscii = $sms->getMessage() === Strings::toAscii($sms->getMessage());
		if (!$isAscii && !$this->unicode) {
			throw new \EuroSmsException('only ASCII text allowed');
		}
		$isLong = Strings::length($sms->getMessage()) > ($isAscii ? 160 : 70);
		if ($isLong && !$this->long) {
			throw new \EuroSmsException('text too long');
		}

		$data = [
			'headers' => [
				'accept' => 'application/json'
			],
			'json' => [
				'iid' => $this->id,
				'sgn' => $this->calcSignature($sms->getSender(), $sms->getPhoneNumber(), $sms->getMessage()),
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
}

<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Maurit\Bundle\SmsBundle\Exception\SmsBranaSkException;
use Maurit\Bundle\SmsBundle\Sms\SmsInterface;


class SmsBranaSkProvider
	implements ProviderInterface
{
	private const SMS_SEND_URI = 'https://api.sms-brana.org/https/send_sms.php';
	private const SMS_STATUS_URI = 'https://api.sms-brana.org/https/check_status.php';

	/** @var string */
	private $login;
	/** @var ClientInterface */
	private $client;
	/** @var string */
	private $password;


	public function __construct()
	{
		$this->setClient(new Client);
	}

	public function setClient(ClientInterface $client): self
	{
		$this->client = $client;
		return $this;
	}

	public function setPassword(?string $password): self
	{
		$this->password = $password;
		return $this;
	}

	public function setLogin(?string $login): self
	{
		$this->login = $login;
		return $this;
	}

	public function send(SmsInterface $sms): string
	{
		try {
			$respRaw = $this->client->request('GET', self::SMS_SEND_URI, $this->getGetSendData($sms))->getBody()->getContents();
			$response = explode(':', $respRaw); // eg: ok:msg:id:credit:credit left
		} catch (\Exception $e) {
			throw new SmsBranaSkException($e->getMessage());
		}

		return trim($response[2]);
	}

	private function getGetSendData(SmsInterface $sms): array
	{
		if (empty($sms->getSender())) {
			throw new SmsBranaSkException('Sender was not set');
		}
		return [
			RequestOptions::QUERY => [
				'login' => $this->login,
				'password' => $this->password,
				'from' => $sms->getSender(),
				'to' => str_replace('+', '00', $sms->getPhoneNumber()),
				'sms_text' => $sms->getMessage()
			],
			RequestOptions::VERIFY => false
		];
	}

	public function balance(): float
	{
		return 0;
	}

	public function check($id): string
	{
		try {
			$respRaw = $this->client->request('GET', self::SMS_STATUS_URI, $this->getGetCheckData($id))->getBody()->getContents();
			$response = explode(':', $respRaw); // eg: STATUS:ID:1
		} catch (\Exception $e) {
			throw new SmsBranaSkException($e->getMessage());
		}
		return $response[2];
	}

	private function getGetCheckData($id): array
	{
		return [
			RequestOptions::QUERY => [
				'login' => $this->login,
				'password' => $this->password,
				'msg_id' => $id
			]
		];
	}
}

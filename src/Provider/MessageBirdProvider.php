<?php declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Maurit\Bundle\SmsBundle\Exception\MessageBirdException;
use Maurit\Bundle\SmsBundle\Sms\SmsInterface;
use Nette\Utils\Json;


class MessageBirdProvider
	implements ProviderInterface
{
	private const SMS_SEND_URI = 'https://rest.messagebird.com/messages';
	private const BALANCE_URI = 'https://rest.messagebird.com/balance';

	private string $accessKey = '';
	private string $originator = '';
	private string $type = '';
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

	public function setAccessKey(string $accessKey): self
	{
		$this->accessKey = $accessKey;
		return $this;
	}

	public function setOriginator(string $originator): self
	{
		$this->originator = $originator;
		return $this;
	}

	public function setType(string $type): self
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * @param SmsInterface $sms
	 * @return string message ID
	 * @throws MessageBirdException
	 */
	public function send(SmsInterface $sms): string
	{
		try {
			$respRaw = $this->client->request('POST', self::SMS_SEND_URI, $this->getPostData($sms))->getBody()->getContents();
		} catch (ClientException $e) {
			$response = json_decode($e->getResponse()->getBody()->getContents());
			$error = current($response->errors);

			throw new MessageBirdException($error->code, $error->description, $error->parameter);
		}
		try {
			$respJson = Json::decode($respRaw, JSON_THROW_ON_ERROR);
		} catch (\Exception $e) {
			throw new MessageBirdException(0, 'Got an invalid JSON response from the server.');
		}
		if ($respJson->errors !== null) {
			$error = current($respJson->errors);
			throw new MessageBirdException($error->code, $error->description, $error->parameter);
		}

		return $respJson->id;
	}

	private function getPostData(SmsInterface $sms): array
	{
		return [
			RequestOptions::HEADERS => [
				'Authorization' => sprintf('AccessKey %s', $this->accessKey)
			],
			RequestOptions::FORM_PARAMS => [
				'originator' => $this->originator,
				'body' => $sms->getMessage(),
				'recipients' => $sms->getPhoneNumber(),
				'type' => $this->type,
				'scheduledDatetime' => $sms->getDateTime()->format(\DateTime::RFC3339),
			],
		];
	}

	public function balance(): float
	{
		try {
			$restRaw = $this->client->request('GET', self::BALANCE_URI, $this->getGetBalanceData());
		} catch (ClientException $e) {
			$response = json_decode($e->getResponse()->getBody()->getContents());
			$error = current($response->errors);
			throw new MessageBirdException($error->code, $error->description, $error->parameter);
		}
		$response = json_decode($restRaw->getBody()->getContents());
		return (float)$response->amount;
	}

	private function getGetBalanceData(): array
	{
		return [
			RequestOptions::HEADERS => [
				'Authorization' => sprintf('AccessKey %s', $this->accessKey)
			]
		];
	}

	public function check($id): string
	{
		return '';
	}
}

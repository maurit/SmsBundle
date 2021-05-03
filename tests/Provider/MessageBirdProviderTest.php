<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\Provider;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Maurit\Bundle\SmsBundle\Exception\MessageBirdException;
use Maurit\Bundle\SmsBundle\Provider\MessageBirdProvider;
use Maurit\Bundle\SmsBundle\Sms\Sms;
use PHPUnit\Framework\TestCase;


class MessageBirdProviderTest
	extends TestCase
{
	use GuzzleClientTrait;

	public function testThatSettersImplementsChainPattern(): void
	{
		$provider = (new MessageBirdProvider)
			->setAccessKey('access_key')
			->setOriginator('originator')
			->setType('sms')
			->setClient(new Client);

		$this->assertInstanceOf(MessageBirdProvider::class, $provider);
	}

	public function testThatExceptionThrownOnInvalidResponseCode(): void
	{
		$this->expectException(MessageBirdException::class);

		(new MessageBirdProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(422, [],
				'{"errors":[{"code": 2, "description": "Request not allowed (incorrect access_key)", "parameter": "access_key"}]}'
			)))
			->send(new Sms('+1234567890', 'Hello World'));
	}

	public function testSend(): void
	{
		$response = (new MessageBirdProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [])))
			->send(new Sms('+1234567890', 'Hello World'));

		$this->assertTrue($response);
	}
}

<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\Provider;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Maurit\Bundle\SmsBundle\Exception\SmsCenterException;
use Maurit\Bundle\SmsBundle\Provider\SmsCenterProvider;
use Maurit\Bundle\SmsBundle\Sms\Sms;
use PHPUnit\Framework\TestCase;


class SmsCenterProviderTest
	extends TestCase
{
	use GuzzleClientTrait;

	public function testThatSettersImplementsChainPattern(): void
	{
		$provider = (new SmsCenterProvider)
			->setLogin('login')
			->setPassword('password')
			->setSender('sender')
			->setFlash(true)
			->setClient(new Client);

		$this->assertInstanceOf(SmsCenterProvider::class, $provider);
	}

	public function testThatExceptionThrownOnInvalidResponseCode(): void
	{
		$this->expectException(SmsCenterException::class);

		(new SmsCenterProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], '{"error": "Nothing to do here", "error_code": 6}')))
			->send(new Sms('+1234567890', 'Hello World'));
	}

	public function testSend(): void
	{
		$response = (new SmsCenterProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], '{"id": 1, "cnt": 1}')))
			->send(new Sms('+1234567890', 'Hello World'));

		$this->assertTrue($response);
	}

	public function testSendWithAdditionalPostData(): void
	{
		$response = (new SmsCenterProvider)
			->setSender('sender')
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], '{"id": 1, "cnt": 1}')))
			->send(new Sms('+1234567890', 'Hello World'));

		$this->assertTrue($response);
	}

	public function testCheck(): void
	{
		$response = (new SmsCenterProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], '{
"status": 1,
"last_date": "12.12.2017 12:12:12",
"last_timestamp": 1513080732,
"err": 0
}')))
			->check(1);

		$this->assertSame('1', $response);
	}

	public function testBalance(): void
	{
		$response = (new SmsCenterProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], '{
"balance": "123.45"
}')))
			->balance();

		$this->assertSame(123.45, $response);
	}
}

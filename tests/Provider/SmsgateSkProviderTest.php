<?php declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Maurit\Bundle\SmsBundle\Exception\SmsgateSkException;
use Maurit\Bundle\SmsBundle\Provider\SmsgateSkProvider;
use Maurit\Bundle\SmsBundle\Sms\Sms;
use PHPUnit\Framework\TestCase;


class SmsgateSkProviderTest
	extends TestCase
{
	use GuzzleClientTrait;


	public function testThatSettersImplementsChainPattern(): void
	{
		$provider = (new SmsgateSkProvider)
			->setToken('token')
			->setClient(new Client);

		self::assertInstanceOf(SmsgateSkProvider::class, $provider);
	}

	public function testThatExceptionThrownOnMissingSender(): void
	{
		$this->expectException(SmsgateSkException::class);

		(new SmsgateSkProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(422, [], '')))
			->send(new Sms('+420766121212', 'Hello World'));
	}

	public function testSend(): void
	{
		$response = (new SmsgateSkProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], '{"result":{"status":"success","description":"","code":"OK"},"message_count":1,"messages":[{"status":"success","code":"OK","description":"","parts":1,"message_id":100427}]}')))
			->setToken('token')
			->send(new Sms('+420766121212', 'Hello World', null, 'Tester'));

		self::assertSame(100427, $response);
	}

	public function testCheck(): void
	{
		$response = (new SmsgateSkProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], '{"result":{"status":"success","description":"","code":"OK"},"message_id":"154786","status":"Doručená","statusId":4,"code":"DELIVERED","deliveryDateTime":"2023-11-28 17:24:08"}')))
			->check('cdef60d8-52a5-47b3-984b-c15c54c196f1');

		self::assertSame('DELIVERED', $response);
	}

	public function testBalance(): void
	{
		$response = (new SmsgateSkProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], "80.693 EUR\n")))
			->balance();

		self::assertSame(0.0, $response);
	}
}

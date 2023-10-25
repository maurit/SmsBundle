<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Maurit\Bundle\SmsBundle\Exception\SmsBranaSkException;
use Maurit\Bundle\SmsBundle\Provider\SmsBranaSkProvider;
use Maurit\Bundle\SmsBundle\Sms\Sms;
use PHPUnit\Framework\TestCase;


class SmsBranaSkProviderTest
	extends TestCase
{
	use GuzzleClientTrait;


	public function testThatSettersImplementsChainPattern(): void
	{
		$provider = (new SmsBranaSkProvider)
			->setLogin('login')
			->setPassword('password')
			->setClient(new Client);

		$this->assertInstanceOf(SmsBranaSkProvider::class, $provider);
	}

	public function testThatExceptionThrownOnMissingSender(): void
	{
		$this->expectException(SmsBranaSkException::class);

		(new SmsBranaSkProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(422, [], '')))
			->send(new Sms('+420766121212', 'Hello World'));
	}

	public function testSend(): void
	{
		$response = (new SmsBranaSkProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], 'OK:Sprava bola odoslana:0d39b9cd0e0ef5e4df8f5a877eafb81c:0.8:8.4')))
			->setLogin('login')
			->send(new Sms('+420766121212', 'Hello World', null, 'Tester'));

		$this->assertSame('0d39b9cd0e0ef5e4df8f5a877eafb81c', $response);
	}

	public function testCheck(): void
	{
		$response = (new SmsBranaSkProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], 'STATUS:ID:1')))
			->check('0d39b9cd0e0ef5e4df8f5a877eafb81c');

		$this->assertSame('1', $response);
	}

	public function testBalance(): void
	{
		$response = (new SmsBranaSkProvider)
			->balance();

		$this->assertSame(0.0, $response);
	}
}

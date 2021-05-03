<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\Provider;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Maurit\Bundle\SmsBundle\Exception\EuroSmsException;
use Maurit\Bundle\SmsBundle\Provider\EuroSmsProvider;
use Maurit\Bundle\SmsBundle\Sms\Sms;
use PHPUnit\Framework\TestCase;


class EuroSmsProviderTest
	extends TestCase
{
	use GuzzleClientTrait;


	public function testThatSettersImplementsChainPattern(): void
	{
		$provider = (new EuroSmsProvider)
			->setId('id')
			->setKey('key')
			->setClient(new Client);

		$this->assertInstanceOf(EuroSmsProvider::class, $provider);
	}

	public function testThatExceptionThrownOnMissingSender(): void
	{
		$this->expectException(EuroSmsException::class);

		(new EuroSmsProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(422, [], '')))
			->send(new Sms('+420766121212', 'Hello World'));
	}

	public function testSend(): void
	{
		$response = (new EuroSmsProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], '{
  "uuid": [
    "cdef60d8-52a5-47b3-984b-c15c54c196f1"
  ],
  "err_code": "ENQUEUED",
  "err_desc": "Message accepted and enqueued to send"
}')))
			->setKey('key')
			->send(new Sms('+420766121212', 'Hello World', null, 'Tester'));

		$this->assertSame('cdef60d8-52a5-47b3-984b-c15c54c196f1', $response);
	}
}

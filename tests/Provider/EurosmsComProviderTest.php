<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\Provider;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Maurit\Bundle\SmsBundle\Exception\EurosmsComException;
use Maurit\Bundle\SmsBundle\Provider\EurosmsComProvider;
use Maurit\Bundle\SmsBundle\Sms\Sms;
use PHPUnit\Framework\TestCase;


class EurosmsComProviderTest
	extends TestCase
{
	use GuzzleClientTrait;


	public function testThatSettersImplementsChainPattern(): void
	{
		$provider = (new EurosmsComProvider)
			->setId('id')
			->setKey('key')
			->setClient(new Client);

		$this->assertInstanceOf(EurosmsComProvider::class, $provider);
	}

	public function testThatExceptionThrownOnMissingSender(): void
	{
		$this->expectException(EurosmsComException::class);

		(new EurosmsComProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(422, [], '')))
			->send(new Sms('+420766121212', 'Hello World'));
	}

	public function testSend(): void
	{
		$response = (new EurosmsComProvider)
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

	public function testCheck(): void
	{
		$response = (new EurosmsComProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], '{"rcpt":421900123456,"carrier":"231.1","dlr_time":"2019-07-16 08:48:00","price":0.029,"snd":"2019-07-16 08:48:49","i":"cdef60d8-52a5-47b3-984b-c15c54c196f1","err_code":"OK","sgmnt":1,"dlr":"DELIVRD"}')))
			->check('cdef60d8-52a5-47b3-984b-c15c54c196f1');

		$this->assertSame('Delivered', $response);
	}

	public function testBalance(): void
	{
		$response = (new EurosmsComProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], "80.693 EUR\n")))
			->balance();

		$this->assertSame(80.693, $response);
	}
}

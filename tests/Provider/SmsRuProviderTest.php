<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\Provider;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Maurit\Bundle\SmsBundle\Exception\SmsRuException;
use Maurit\Bundle\SmsBundle\Provider\SmsRuProvider;
use Maurit\Bundle\SmsBundle\Sms\Sms;
use PHPUnit\Framework\TestCase;


class SmsRuProviderTest
	extends TestCase
{
	use GuzzleClientTrait;

	public function testThatSettersImplementsChainPattern(): void
	{
		$provider = (new SmsRuProvider)
			->setApiId('id')
			->setFrom('from')
			->setTest(false)
			->setClient(new Client);

		$this->assertInstanceOf(SmsRuProvider::class, $provider);
	}

	public function testThatExceptionThrownOnInvalidResponseCode(): void
	{
		$this->expectException(SmsRuException::class);

		(new SmsRuProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], 302)))
			->send(new Sms('+1234567890', 'Hello World'));
	}

	public function testSend(): void
	{
		$response = (new SmsRuProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], 100)))
			->send(new Sms('+1234567890', 'Hello World'));

		$this->assertTrue($response);
	}

	public function testCheck(): void
	{
		$response = (new SmsRuProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], '{
    "status": "OK",
    "status_code": 100,
    "sms": {
        "000000-000001": {
            "status": "OK",
            "status_code": 103,
            "cost": 0.50,
            "status_text": "Сообщение доставлено"
        }
    } ,
    "balance": 4122.56
}')))
			->check('000000-000001');

		$this->assertSame('Сообщение доставлено', $response);
	}

	public function testBalance(): void
	{
		$response = (new SmsRuProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], '{
    "status": "OK",
    "status_code": 100,
    "balance": 4762.58
}')))
			->balance();

		$this->assertSame(4762.58, $response);
	}
}

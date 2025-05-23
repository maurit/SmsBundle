<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Maurit\Bundle\SmsBundle\Exception\SmsAeroException;
use Maurit\Bundle\SmsBundle\Provider\SmsAeroProvider;
use Maurit\Bundle\SmsBundle\Sms\Sms;
use PHPUnit\Framework\TestCase;


class SmsAeroProviderTest
	extends TestCase
{
	use GuzzleClientTrait;


	public function testThatSettersImplementsChainPattern(): void
	{
		$provider = (new SmsAeroProvider)
			->setApiKey('key')
			->setUser('user')
			->setChannel('channel')
			->setSign('sign')
			->setClient(new Client());

		self::assertInstanceOf(SmsAeroProvider::class, $provider);
	}

	public function testThatExceptionThrownOnInvalidResponseCode(): void
	{
		$this->expectException(SmsAeroException::class);

		(new SmsAeroProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], '{"success": false}')))
			->send(new Sms('+1234567890', 'Hello World'));
	}

	public function testSend(): void
	{
		$response = (new SmsAeroProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], '{
    "success": true,
    "data": [
        {
            "id": 1,
            "from": "SMS Aero",
            "number": "+1234567890",
            "text": "Hello World",
            "status": 0,
            "extendStatus": "queue",
            "channel": "FREE SIGN",
            "cost": 1.95,
            "dateCreate": 1510656981,
            "dateSend": 1510656981
        }
    ],
    "message": null
}')))
			->send(new Sms('+1234567890', 'Hello World'));

		self::assertSame(1, $response);
	}

	public function testCheck(): void
	{
		$response = (new SmsAeroProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], '{
        "success": true,
        "data": {
            "id": 1,
            "from": "SMS Aero",
            "number": "79990000000",
            "text": "your text",
            "status": 1,
            "extendStatus": "delivery",
            "channel": "DIRECT",
            "cost": "1.95",
            "dateCreate": 1510656981,
            "dateSend": 1510656981,
            "dateAnswer": 1510656987
        },
        "message": null
    }')))
			->check(1);

		self::assertSame('delivery', $response);
	}

	public function testBalance(): void
	{
		$response = (new SmsAeroProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], '{
        "success": true,
        "data": {
            "balance": 1389.26
        },
        "message": null
    }')))
			->balance();

		self::assertSame(1389.26, $response);
	}
}

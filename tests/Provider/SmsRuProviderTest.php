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

		self::assertInstanceOf(SmsRuProvider::class, $provider);
	}

	public function testThatExceptionThrownOnInvalidResponseCode(): void
	{
		$this->expectException(SmsRuException::class);

		(new SmsRuProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], '{"status":"error","status_code":302, "sms":{}}')))
			->send(new Sms('+1234567890', 'Hello World'));
	}

	public function testSend(): void
	{
		$response = (new SmsRuProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], '{
    "status": "OK",
    "status_code": 100,
    "sms": {
        "79255070602": {
            "status": "OK",
            "status_code": 100,
            "sms_id": "000000-10000000"
        },
        "74993221627": {
            "status": "ERROR",
            "status_code": 207,
            "status_text": "На этот номер (или один из номеров) нельзя отправлять сообщения, либо указано более 100 номеров в списке получателей"
        }
    } ,
    "balance": 4122.56
}')))
			->send(new Sms('+1234567890', 'Hello World'));

		self::assertEquals('79255070602', $response);
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

		self::assertSame('Сообщение доставлено', $response);
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

		self::assertSame(4762.58, $response);
	}
}

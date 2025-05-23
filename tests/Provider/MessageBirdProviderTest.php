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

		self::assertInstanceOf(MessageBirdProvider::class, $provider);
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

	public function testThatExceptionThrownOnInvalidResponseCodeNoParameter(): void
	{
		$this->expectException(MessageBirdException::class);

		(new MessageBirdProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(422, [],
				'{"errors":[{"code": 2, "description": "Plain error"}]}'
			)))
			->send(new Sms('+1234567890', 'Hello World'));
	}

	public function testSend(): void
	{
		$response = (new MessageBirdProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], '{
  "id":"e8077d803532c0b5937c639b60216938",
  "href":"https://rest.messagebird.com/messages/e8077d803532c0b5937c639b60216938",
  "direction":"mt",
  "type":"sms",
  "originator":"MessageBird",
  "body":"The message to be sent",
  "reference":"the-client-reference",
  "validity":null,
  "gateway":240,
  "typeDetails":{

  },
  "datacoding":"plain",
  "mclass":1,
  "scheduledDatetime":null,
  "createdDatetime":"2016-04-29T09:42:26+00:00",
  "recipients":{
    "totalCount":1,
    "totalSentCount":1,
    "totalDeliveredCount":1,
    "totalDeliveryFailedCount":0,
    "items":[
      {
        "recipient":31612345678,
        "status":"sent",
        "statusReason":"successfully delivered",
        "statusErrorCode":null,
        "statusDatetime":"2016-04-29T09:42:26+00:00",
        "recipientCountry":"Netherlands",
        "recipientCountryPrefix":31,
        "recipientOperator":"KPN",
        "mccmnc":"20408",
        "mcc":"204",
        "mnc":"08",
        "messageLength":22,
        "messagePartCount":1,
        "price":{
            "amount":0.075,
            "currency":"EUR"
        }
      }
    ]
  }
}')))
			->send(new Sms('+1234567890', 'Hello World'));

		self::assertSame('e8077d803532c0b5937c639b60216938', $response);
	}

	public function testBalance(): void
	{
		$response = (new MessageBirdProvider)
			->setClient($this->getClientWithPreparedResponse(new Response(200, [], '{
  "payment": "prepaid",
  "type": "euros",
  "amount": 103
}')))
			->balance();

		self::assertSame(103.0, $response);
	}
}

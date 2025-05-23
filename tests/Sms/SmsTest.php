<?php declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\Sms;

use Maurit\Bundle\SmsBundle\Sms\Sms;
use PHPUnit\Framework\TestCase;


final class SmsTest
	extends TestCase
{
	public function testGetCorrectPhoneNumber(): void
	{
		$number = '+1234567890';
		$sms = new Sms($number, 'Hello world');

		self::assertEquals($number, $sms->getPhoneNumber());
	}

	public function testGetCorrectMessage(): void
	{
		$message = 'Hello World';
		$sms = new Sms('+1234567890', $message);

		self::assertEquals($message, $sms->getMessage());
	}

	public function testDateTimeEmptyParameter(): void
	{
		$sms = new Sms('+1234567890', 'Hello World', null);

		$dt = $sms->getDateTime();
		$ts = time();

		self::assertInstanceOf(\DateTime::class, $dt);
		self::assertEquals($ts, $dt->getTimestamp());
	}

	public function testGetCorrectDateTime(): void
	{
		$dt = (new \DateTime)->add(new \DateInterval('PT5M'));
		$sms = new Sms('+1234567890', 'Hello World', $dt);

		self::assertEquals($dt, $sms->getDateTime());
	}

	public function testGetEmptySender(): void
	{
		$sms = new Sms('+1234567890', 'Hello World');

		self::assertSame('', $sms->getSender());
	}

	public function testGetCorrectSender(): void
	{
		$sender = 'Tester';
		$sms = new Sms('+1234567890', 'Hello World', null, $sender);

		self::assertSame($sender, $sms->getSender());
	}
}

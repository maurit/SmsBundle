<?php

namespace Maurit\Bundle\SmsBundle\Tests\Fixture\Provider;

use Maurit\Bundle\SmsBundle\Provider\ProviderInterface;
use Maurit\Bundle\SmsBundle\Sms\SmsInterface;


class ProviderFixture
{
	public static function getProvider(): ProviderInterface
	{
		return new class implements ProviderInterface {
			public function send(SmsInterface $sms): string|int
			{
				return 1;
			}

			public function balance(): float
			{
				return 0;
			}

			public function check($id): string
			{
				return '';
			}
		};
	}
}

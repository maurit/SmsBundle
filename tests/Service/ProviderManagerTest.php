<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\Service;

use Maurit\Bundle\SmsBundle\Service\ProviderManager;
use Maurit\Bundle\SmsBundle\Tests\Fixture\Provider\ProviderFixture;
use PHPUnit\Framework\TestCase;


class ProviderManagerTest
	extends TestCase
{
	public function testGetNonExistProvider(): void
	{
		$this->expectException(\OutOfBoundsException::class);

		(new ProviderManager)->getProvider('NonExistProvider');
	}

	public function testSetWrongProviderType(): void
	{
		$this->expectException(\TypeError::class);

		(new ProviderManager)->addProvider('Foo', 'Bar');
	}

	public function testGetExistsProvider(): void
	{
		$provider = ProviderFixture::getProvider();
		$name = 'TestProvider';
		$pm = new ProviderManager;

		$pm->addProvider($name, $provider);

		self::assertEquals($provider, $pm->getProvider($name));
	}
}

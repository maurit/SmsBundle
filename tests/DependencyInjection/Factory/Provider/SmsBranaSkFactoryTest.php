<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\DependencyInjection\Factory\Provider;

use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\SmsBranaSkProviderFactory;
use Maurit\Bundle\SmsBundle\Provider\SmsBranaSkProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;


class SmsBranaSkFactoryTest
	extends TestCase
{
	use ProviderTestTrait;


	public function testGetCorrectName(): void
	{
		self::assertEquals('sms_brana_sk', (new SmsBranaSkProviderFactory)->getName());
	}

	public function testConfigurationHasAllRequiredParameters(): void
	{
		$def = $this->getFactoryConfiguration(new SmsBranaSkProviderFactory);

		self::assertArrayHasKey('login', $def);
		self::assertArrayHasKey('password', $def);
	}

	public function testConfigurationHasCorrectTypes(): void
	{
		$def = $this->getFactoryConfiguration(new SmsBranaSkProviderFactory);

		self::assertInstanceOf(ScalarNodeDefinition::class, $def['login']);
		self::assertInstanceOf(ScalarNodeDefinition::class, $def['password']);
	}

	public function testThatDefinitionHasAllRequiredMethods(): void
	{
		$prototypeMethods = $this->getPrototypeMethods(new SmsBranaSkProvider);
		$calls = $this->getDefinitionMethodCalls(new SmsBranaSkProviderFactory, ['login', 'password']);

		foreach ($calls as $call) {
			self::assertContains($call, $prototypeMethods);
		}
	}
}

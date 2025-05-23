<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\DependencyInjection\Factory\Provider;

use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\SmsAeroProviderFactory;
use Maurit\Bundle\SmsBundle\Provider\SmsAeroProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;


class SmsAeroProviderFactoryTest
	extends TestCase
{
	use ProviderTestTrait;


	public function testGetCorrectName(): void
	{
		self::assertEquals('sms_aero', (new SmsAeroProviderFactory)->getName());
	}

	public function testConfigurationHasAllRequiredParameters(): void
	{
		$def = $this->getFactoryConfiguration(new SmsAeroProviderFactory);

		self::assertArrayHasKey('user', $def);
		self::assertArrayHasKey('api_key', $def);
		self::assertArrayHasKey('sign', $def);
		self::assertArrayHasKey('channel', $def);
	}

	public function testConfigurationHasCorrectTypes(): void
	{
		$def = $this->getFactoryConfiguration(new SmsAeroProviderFactory);

		self::assertInstanceOf(ScalarNodeDefinition::class, $def['user']);
		self::assertInstanceOf(ScalarNodeDefinition::class, $def['api_key']);
		self::assertInstanceOf(ScalarNodeDefinition::class, $def['sign']);
		self::assertInstanceOf(ScalarNodeDefinition::class, $def['channel']);
	}

	public function testThatDefinitionHasAllRequiredMethods(): void
	{
		$prototypeMethods = $this->getPrototypeMethods(new SmsAeroProvider);
		$calls = $this->getDefinitionMethodCalls(new SmsAeroProviderFactory, ['user', 'api_key', 'sign', 'channel']);

		foreach ($calls as $call) {
			self::assertContains($call, $prototypeMethods);
		}
	}
}

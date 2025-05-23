<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\DependencyInjection\Factory\Provider;

use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\SmsCenterProviderFactory;
use Maurit\Bundle\SmsBundle\Provider\SmsCenterProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\BooleanNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;


class SmsCenterProviderFactoryTest
	extends TestCase
{
	use ProviderTestTrait;


	public function testGetCorrectName(): void
	{
		self::assertEquals('sms_center', (new SmsCenterProviderFactory)->getName());
	}

	public function testConfigurationHasAllRequiredParameters(): void
	{
		$def = $this->getFactoryConfiguration(new SmsCenterProviderFactory);

		self::assertArrayHasKey('login', $def);
		self::assertArrayHasKey('password', $def);
		self::assertArrayHasKey('sender', $def);
		self::assertArrayHasKey('flash', $def);
	}

	public function testConfigurationHasCorrectTypes(): void
	{
		$def = $this->getFactoryConfiguration(new SmsCenterProviderFactory);

		self::assertInstanceOf(ScalarNodeDefinition::class, $def['login']);
		self::assertInstanceOf(ScalarNodeDefinition::class, $def['password']);
		self::assertInstanceOf(ScalarNodeDefinition::class, $def['sender']);
		self::assertInstanceOf(BooleanNodeDefinition::class, $def['flash']);
	}

	public function testThatDefinitionHasAllRequiredMethods(): void
	{
		$prototypeMethods = $this->getPrototypeMethods(new SmsCenterProvider);
		$calls = $this->getDefinitionMethodCalls(new SmsCenterProviderFactory, ['login', 'password', 'sender', 'flash']);

		foreach ($calls as $call) {
			self::assertContains($call, $prototypeMethods);
		}
	}
}

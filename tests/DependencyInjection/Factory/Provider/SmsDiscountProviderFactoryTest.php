<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\DependencyInjection\Factory\Provider;

use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\SmsDiscountProviderFactory;
use Maurit\Bundle\SmsBundle\Provider\SmsDiscountProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\BooleanNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;


class SmsDiscountProviderFactoryTest
	extends TestCase
{
	use ProviderTestTrait;


	public function testGetCorrectName(): void
	{
		self::assertEquals('sms_discount', (new SmsDiscountProviderFactory)->getName());
	}

	public function testConfigurationHasAllRequiredParameters(): void
	{
		$def = $this->getFactoryConfiguration(new SmsDiscountProviderFactory);

		self::assertArrayHasKey('login', $def);
		self::assertArrayHasKey('password', $def);
		self::assertArrayHasKey('sender', $def);
		self::assertArrayHasKey('flash', $def);
	}

	public function testConfigurationHasCorrectTypes(): void
	{
		$def = $this->getFactoryConfiguration(new SmsDiscountProviderFactory);

		self::assertInstanceOf(ScalarNodeDefinition::class, $def['login']);
		self::assertInstanceOf(ScalarNodeDefinition::class, $def['password']);
		self::assertInstanceOf(ScalarNodeDefinition::class, $def['sender']);
		self::assertInstanceOf(BooleanNodeDefinition::class, $def['flash']);
	}

	public function testThatDefinitionHasAllRequiredMethods(): void
	{
		$prototypeMethods = $this->getPrototypeMethods(new SmsDiscountProvider);
		$calls = $this->getDefinitionMethodCalls(new SmsDiscountProviderFactory, ['login', 'password', 'sender', 'flash']);

		foreach ($calls as $call) {
			self::assertContains($call, $prototypeMethods);
		}
	}
}

<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\DependencyInjection\Factory\Provider;

use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\SmsgateSkProviderFactory;
use Maurit\Bundle\SmsBundle\Provider\SmsgateSkProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\BooleanNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;


class SmsgateSkProviderFactoryTest
	extends TestCase
{
	use ProviderTestTrait;


	public function testGetCorrectName(): void
	{
		self::assertEquals('smsgate_sk', (new SmsgateSkProviderFactory)->getName());
	}

	public function testConfigurationHasAllRequiredParameters(): void
	{
		$def = $this->getFactoryConfiguration(new SmsgateSkProviderFactory);

		self::assertArrayHasKey('token', $def);
	}

	public function testConfigurationHasCorrectTypes(): void
	{
		$def = $this->getFactoryConfiguration(new SmsgateSkProviderFactory);

		self::assertInstanceOf(ScalarNodeDefinition::class, $def['token']);
		self::assertInstanceOf(BooleanNodeDefinition::class, $def['unicode']);
		self::assertInstanceOf(BooleanNodeDefinition::class, $def['textNumbers']);
	}

	public function testThatDefinitionHasAllRequiredMethods(): void
	{
		$prototypeMethods = $this->getPrototypeMethods(new SmsgateSkProvider);
		$calls = $this->getDefinitionMethodCalls(new SmsgateSkProviderFactory, ['token', 'unicode', 'textNumbers']);

		foreach ($calls as $call) {
			self::assertContains($call, $prototypeMethods);
		}
	}
}

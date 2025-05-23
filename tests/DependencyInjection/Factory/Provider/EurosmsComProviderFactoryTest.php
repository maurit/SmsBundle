<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\DependencyInjection\Factory\Provider;

use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\EurosmsComProviderFactory;
use Maurit\Bundle\SmsBundle\Provider\EurosmsComProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\BooleanNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;


class EurosmsComProviderFactoryTest
	extends TestCase
{
	use ProviderTestTrait;


	public function testGetCorrectName(): void
	{
		self::assertEquals('eurosms_com', (new EurosmsComProviderFactory)->getName());
	}

	public function testConfigurationHasAllRequiredParameters(): void
	{
		$def = $this->getFactoryConfiguration(new EurosmsComProviderFactory);

		self::assertArrayHasKey('id', $def);
		self::assertArrayHasKey('key', $def);
		self::assertArrayHasKey('test', $def);
		self::assertArrayHasKey('unicode', $def);
		self::assertArrayHasKey('long', $def);
	}

	public function testConfigurationHasCorrectTypes(): void
	{
		$def = $this->getFactoryConfiguration(new EurosmsComProviderFactory);

		self::assertInstanceOf(ScalarNodeDefinition::class, $def['id']);
		self::assertInstanceOf(ScalarNodeDefinition::class, $def['key']);
		self::assertInstanceOf(BooleanNodeDefinition::class, $def['test']);
		self::assertInstanceOf(BooleanNodeDefinition::class, $def['unicode']);
		self::assertInstanceOf(BooleanNodeDefinition::class, $def['long']);
	}

	public function testThatDefinitionHasAllRequiredMethods(): void
	{
		$prototypeMethods = $this->getPrototypeMethods(new EurosmsComProvider);
		$calls = $this->getDefinitionMethodCalls(new EurosmsComProviderFactory, ['id', 'key', 'test', 'unicode', 'long']);

		foreach ($calls as $call) {
			self::assertContains($call, $prototypeMethods);
		}
	}
}

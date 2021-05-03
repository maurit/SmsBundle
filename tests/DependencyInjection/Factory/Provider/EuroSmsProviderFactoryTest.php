<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\DependencyInjection\Factory\Provider;


use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\EuroSmsProviderFactory;
use Maurit\Bundle\SmsBundle\Provider\EuroSmsProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\BooleanNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;


class EuroSmsProviderFactoryTest
	extends TestCase
{
	use ProviderTestTrait;


	public function testGetCorrectName(): void
	{
		$this->assertEquals('euro_sms', (new EuroSmsProviderFactory)->getName());
	}

	public function testConfigurationHasAllRequiredParameters(): void
	{
		$def = $this->getFactoryConfiguration(new EuroSmsProviderFactory);

		$this->assertArrayHasKey('id', $def);
		$this->assertArrayHasKey('key', $def);
		$this->assertArrayHasKey('test', $def);
		$this->assertArrayHasKey('unicode', $def);
		$this->assertArrayHasKey('long', $def);
	}

	public function testConfigurationHasCorrectTypes(): void
	{
		$def = $this->getFactoryConfiguration(new EuroSmsProviderFactory);

		$this->assertInstanceOf(ScalarNodeDefinition::class, $def['id']);
		$this->assertInstanceOf(ScalarNodeDefinition::class, $def['key']);
		$this->assertInstanceOf(BooleanNodeDefinition::class, $def['test']);
		$this->assertInstanceOf(BooleanNodeDefinition::class, $def['unicode']);
		$this->assertInstanceOf(BooleanNodeDefinition::class, $def['long']);
	}

	public function testThatDefinitionHasAllRequiredMethods(): void
	{
		$prototypeMethods = $this->getPrototypeMethods(new EuroSmsProvider);
		$calls = $this->getDefinitionMethodCalls(new EuroSmsProviderFactory, ['id', 'key', 'test', 'unicode', 'long']);

		foreach ($calls as $call) {
			$this->assertContains($call, $prototypeMethods);
		}
	}
}

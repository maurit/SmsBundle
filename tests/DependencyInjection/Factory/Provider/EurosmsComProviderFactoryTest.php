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
		$this->assertEquals('eurosms_com', (new EurosmsComProviderFactory)->getName());
	}

	public function testConfigurationHasAllRequiredParameters(): void
	{
		$def = $this->getFactoryConfiguration(new EurosmsComProviderFactory);

		$this->assertArrayHasKey('id', $def);
		$this->assertArrayHasKey('key', $def);
		$this->assertArrayHasKey('test', $def);
		$this->assertArrayHasKey('unicode', $def);
		$this->assertArrayHasKey('long', $def);
	}

	public function testConfigurationHasCorrectTypes(): void
	{
		$def = $this->getFactoryConfiguration(new EurosmsComProviderFactory);

		$this->assertInstanceOf(ScalarNodeDefinition::class, $def['id']);
		$this->assertInstanceOf(ScalarNodeDefinition::class, $def['key']);
		$this->assertInstanceOf(BooleanNodeDefinition::class, $def['test']);
		$this->assertInstanceOf(BooleanNodeDefinition::class, $def['unicode']);
		$this->assertInstanceOf(BooleanNodeDefinition::class, $def['long']);
	}

	public function testThatDefinitionHasAllRequiredMethods(): void
	{
		$prototypeMethods = $this->getPrototypeMethods(new EurosmsComProvider);
		$calls = $this->getDefinitionMethodCalls(new EurosmsComProviderFactory, ['id', 'key', 'test', 'unicode', 'long']);

		foreach ($calls as $call) {
			$this->assertContains($call, $prototypeMethods);
		}
	}
}

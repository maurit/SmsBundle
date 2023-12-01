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
		$this->assertEquals('smsgate_sk', (new SmsgateSkProviderFactory)->getName());
	}

	public function testConfigurationHasAllRequiredParameters(): void
	{
		$def = $this->getFactoryConfiguration(new SmsgateSkProviderFactory);

		$this->assertArrayHasKey('token', $def);
	}

	public function testConfigurationHasCorrectTypes(): void
	{
		$def = $this->getFactoryConfiguration(new SmsgateSkProviderFactory);

		$this->assertInstanceOf(ScalarNodeDefinition::class, $def['token']);
		$this->assertInstanceOf(BooleanNodeDefinition::class, $def['unicode']);
		$this->assertInstanceOf(BooleanNodeDefinition::class, $def['textNumbers']);
	}

	public function testThatDefinitionHasAllRequiredMethods(): void
	{
		$prototypeMethods = $this->getPrototypeMethods(new SmsgateSkProvider);
		$calls = $this->getDefinitionMethodCalls(new SmsgateSkProviderFactory, ['token', 'unicode', 'textNumbers']);

		foreach ($calls as $call) {
			$this->assertContains($call, $prototypeMethods);
		}
	}
}

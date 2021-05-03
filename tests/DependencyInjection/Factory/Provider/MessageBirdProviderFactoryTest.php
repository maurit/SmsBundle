<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\DependencyInjection\Factory\Provider;


use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\MessageBirdProviderFactory;
use Maurit\Bundle\SmsBundle\Provider\MessageBirdProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;


class MessageBirdProviderFactoryTest
	extends TestCase
{
	use ProviderTestTrait;

	public function testGetCorrectName(): void
	{
		$this->assertEquals('message_bird', (new MessageBirdProviderFactory)->getName());
	}

	public function testConfigurationHasAllRequiredParameters(): void
	{
		$def = $this->getFactoryConfiguration(new MessageBirdProviderFactory);

		$this->assertArrayHasKey('access_key', $def);
		$this->assertArrayHasKey('originator', $def);
		$this->assertArrayHasKey('type', $def);
	}

	public function testConfigurationHasCorrectTypes(): void
	{
		$def = $this->getFactoryConfiguration(new MessageBirdProviderFactory);

		$this->assertInstanceOf(ScalarNodeDefinition::class, $def['access_key']);
		$this->assertInstanceOf(ScalarNodeDefinition::class, $def['originator']);
		$this->assertInstanceOf(ScalarNodeDefinition::class, $def['type']);
	}

	public function testThatDefinitionHasAllRequiredMethods(): void
	{
		$prototypeMethods = $this->getPrototypeMethods(new MessageBirdProvider);
		$calls = $this->getDefinitionMethodCalls(new MessageBirdProviderFactory, ['access_key', 'originator', 'type']);

		foreach ($calls as $call) {
			$this->assertContains($call, $prototypeMethods);
		}
	}
}

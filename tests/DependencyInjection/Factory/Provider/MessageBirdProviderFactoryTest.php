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
		self::assertEquals('message_bird', (new MessageBirdProviderFactory)->getName());
	}

	public function testConfigurationHasAllRequiredParameters(): void
	{
		$def = $this->getFactoryConfiguration(new MessageBirdProviderFactory);

		self::assertArrayHasKey('access_key', $def);
		self::assertArrayHasKey('originator', $def);
		self::assertArrayHasKey('type', $def);
	}

	public function testConfigurationHasCorrectTypes(): void
	{
		$def = $this->getFactoryConfiguration(new MessageBirdProviderFactory);

		self::assertInstanceOf(ScalarNodeDefinition::class, $def['access_key']);
		self::assertInstanceOf(ScalarNodeDefinition::class, $def['originator']);
		self::assertInstanceOf(ScalarNodeDefinition::class, $def['type']);
	}

	public function testThatDefinitionHasAllRequiredMethods(): void
	{
		$prototypeMethods = $this->getPrototypeMethods(new MessageBirdProvider);
		$calls = $this->getDefinitionMethodCalls(new MessageBirdProviderFactory, ['access_key', 'originator', 'type']);

		foreach ($calls as $call) {
			self::assertContains($call, $prototypeMethods);
		}
	}
}

<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\DependencyInjection\Factory\Provider;

use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\SmsRuProviderFactory;
use Maurit\Bundle\SmsBundle\Provider\SmsRuProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\BooleanNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;


class SmsRuProviderFactoryTest
	extends TestCase
{
	use ProviderTestTrait;


	public function testGetCorrectName(): void
	{
		self::assertEquals('sms_ru', (new SmsRuProviderFactory)->getName());
	}

	public function testConfigurationHasAllRequiredParameters(): void
	{
		$def = $this->getFactoryConfiguration(new SmsRuProviderFactory);

		self::assertArrayHasKey('api_id', $def);
		self::assertArrayHasKey('from', $def);
		self::assertArrayHasKey('test', $def);
	}

	public function testConfigurationHasCorrectTypes(): void
	{
		$def = $this->getFactoryConfiguration(new SmsRuProviderFactory);

		self::assertInstanceOf(ScalarNodeDefinition::class, $def['api_id']);
		self::assertInstanceOf(ScalarNodeDefinition::class, $def['from']);
		self::assertInstanceOf(BooleanNodeDefinition::class, $def['test']);
	}

	public function testThatDefinitionHasAllRequiredMethods(): void
	{
		$prototypeMethods = $this->getPrototypeMethods(new SmsRuProvider);
		$calls = $this->getDefinitionMethodCalls(new SmsRuProviderFactory, ['api_id', 'from', 'test']);

		foreach ($calls as $call) {
			self::assertContains($call, $prototypeMethods);
		}
	}
}

<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\DependencyInjection\Factory\Provider;

use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\SmsBranaSkProviderFactory;
use Maurit\Bundle\SmsBundle\Provider\SmsBranaSkProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;


class SmsBranaSkFactoryTest
	extends TestCase
{
	use ProviderTestTrait;


	public function testGetCorrectName(): void
	{
		$this->assertEquals('sms_brana_sk', (new SmsBranaSkProviderFactory)->getName());
	}

	public function testConfigurationHasAllRequiredParameters(): void
	{
		$def = $this->getFactoryConfiguration(new SmsBranaSkProviderFactory);

		$this->assertArrayHasKey('login', $def);
		$this->assertArrayHasKey('password', $def);
	}

	public function testConfigurationHasCorrectTypes(): void
	{
		$def = $this->getFactoryConfiguration(new SmsBranaSkProviderFactory);

		$this->assertInstanceOf(ScalarNodeDefinition::class, $def['login']);
		$this->assertInstanceOf(ScalarNodeDefinition::class, $def['password']);
	}

	public function testThatDefinitionHasAllRequiredMethods(): void
	{
		$prototypeMethods = $this->getPrototypeMethods(new SmsBranaSkProvider);
		$calls = $this->getDefinitionMethodCalls(new SmsBranaSkProviderFactory, ['login', 'password']);

		foreach ($calls as $call) {
			$this->assertContains($call, $prototypeMethods);
		}
	}
}

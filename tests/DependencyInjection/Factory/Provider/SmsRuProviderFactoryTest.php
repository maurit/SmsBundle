<?php

declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\DependencyInjection\Factory\Provider;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Config\Definition\Builder\BooleanNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;
use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\ProviderFactoryInterface;
use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\SmsRuProviderFactory;
use Maurit\Bundle\SmsBundle\Provider\ProviderInterface;
use Maurit\Bundle\SmsBundle\Provider\SmsRuProvider;

class SmsRuProviderFactoryTest extends TestCase
{
    use ProviderTestTrait;

    public function testGetCorrectName(): void
    {
        $this->assertEquals('sms_ru', (new SmsRuProviderFactory())->getName());
    }

    public function testConfigurationHasAllRequiredParameters(): void
    {
        $def = $this->getFactoryConfiguration(new SmsRuProviderFactory());

        $this->assertArrayHasKey('api_id', $def);
        $this->assertArrayHasKey('from', $def);
        $this->assertArrayHasKey('test', $def);
    }

    public function testConfigurationHasCorrectTypes(): void
    {
        $def = $this->getFactoryConfiguration(new SmsRuProviderFactory());

        $this->assertInstanceOf(ScalarNodeDefinition::class, $def['api_id']);
        $this->assertInstanceOf(ScalarNodeDefinition::class, $def['from']);
        $this->assertInstanceOf(BooleanNodeDefinition::class, $def['test']);
    }

    public function testThatDefinitionHasAllRequiredMethods(): void
    {
        $prototypeMethods = $this->getPrototypeMethods(new SmsRuProvider());
        $calls = $this->getDefinitionMethodCalls(new SmsRuProviderFactory(), ['api_id', 'from', 'test']);

        foreach ($calls as $call) {
            $this->assertContains($call, $prototypeMethods);
        }
    }
}
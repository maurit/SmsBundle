<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\DependencyInjection\Factory\Provider;

use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\ProviderFactoryInterface;
use Maurit\Bundle\SmsBundle\Provider\ProviderInterface;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;


trait ProviderTestTrait
{
	protected function getFactoryConfiguration(ProviderFactoryInterface $providerFactory): array
	{
		$definition = new ArrayNodeDefinition(null);
		$providerFactory->buildConfiguration($definition);

		return $definition->getChildNodeDefinitions();
	}

	protected function getPrototypeMethods(ProviderInterface $provider): array
	{
		$ref = new ReflectionClass($provider);

		return array_map(function (ReflectionMethod $method) {
			return $method->getName();
		}, $ref->getMethods(ReflectionMethod::IS_PUBLIC));
	}

	protected function getDefinitionMethodCalls(ProviderFactoryInterface $factory, array $config): array
	{
		$def = $factory->getDefinition(array_flip($config));

		return array_map(function (array $item) {
			return current($item);
		}, $def->getMethodCalls());
	}
}

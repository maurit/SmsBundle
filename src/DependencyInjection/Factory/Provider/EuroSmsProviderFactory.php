<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider;


use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;


class EuroSmsProviderFactory
	extends AbstractProviderFactory
{
	public function getName(): string
	{
		return 'euro_sms';
	}

	public function getDefinition(array $config): ChildDefinition
	{
		return (new ChildDefinition('maurit_sms.prototype.provider.euro_sms'))
			->addMethodCall('setId', [$config['id']])
			->addMethodCall('setKey', [$config['key']])
			->addMethodCall('setTest', [$config['test']])
			->addMethodCall('setUnicode', [$config['unicode']])
			->addMethodCall('setLong', [$config['long']])
			;
	}

	public function buildConfiguration(ArrayNodeDefinition $nodeDefinition): void
	{
		$nodeDefinition
			->children()
				->scalarNode('id')
					->isRequired()
					->cannotBeEmpty()
				->end()
				->scalarNode('key')
					->isRequired()
					->cannotBeEmpty()
				->end()
				->booleanNode('test')
					->defaultFalse()
				->end()
				->booleanNode('unicode')
					->defaultFalse()
				->end()
				->booleanNode('long')
					->defaultFalse()
				->end()
			->end();
	}
}

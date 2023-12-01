<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;


class SmsgateSkProviderFactory
	extends AbstractProviderFactory
{
	public function getName(): string
	{
		return 'smsgate_sk';
	}

	public function getDefinition(array $config): ChildDefinition
	{
		return (new ChildDefinition('maurit_sms.prototype.provider.smsgate_sk'))
			->addMethodCall('setToken', [$config['token']])
			->addMethodCall('setTextNumbers', [$config['textNumbers']])
			->addMethodCall('setUnicode', [$config['unicode']])
			;
	}

	public function buildConfiguration(ArrayNodeDefinition $nodeDefinition): void
	{
		$nodeDefinition
			->children()
				->scalarNode('token')
					->isRequired()
					->cannotBeEmpty()
				->end()
				->booleanNode('textNumbers')
					->defaultFalse()
				->end()
				->booleanNode('unicode')
					->defaultTrue()
				->end()
			->end();
	}
}

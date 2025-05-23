<?php declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;


class SmsRuProviderFactory
	extends AbstractProviderFactory
{
	public function getName(): string
	{
		return 'sms_ru';
	}

	public function getDefinition(array $config): ChildDefinition
	{
		return (new ChildDefinition('maurit_sms.prototype.provider.sms_ru'))
			->addMethodCall('setApiId', [$config['api_id']])
			->addMethodCall('setFrom', [$config['from']])
			->addMethodCall('setTest', [$config['test']]);
	}

	public function buildConfiguration(ArrayNodeDefinition $arrayNodeDefinition): void
	{
		$arrayNodeDefinition
			->children()
				->scalarNode('api_id')
					->isRequired()
					->cannotBeEmpty()
				->end()
				->scalarNode('from')
					->defaultNull()
				->end()
				->booleanNode('test')
					->defaultFalse()
				->end()
			->end();
	}
}

<?php declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;


class SmsDiscountProviderFactory
	extends AbstractProviderFactory
{
	public function getName(): string
	{
		return 'sms_discount';
	}

	public function getDefinition(array $config): ChildDefinition
	{
		return (new ChildDefinition('maurit_sms.prototype.provider.sms_discount'))
			->addMethodCall('setLogin', [$config['login']])
			->addMethodCall('setPassword', [$config['password']])
			->addMethodCall('setSender', [$config['sender']])
			->addMethodCall('setFlash', [$config['flash']]);
	}

	public function buildConfiguration(ArrayNodeDefinition $arrayNodeDefinition): void
	{
		$arrayNodeDefinition
			->children()
				->scalarNode('login')
					->isRequired()
					->cannotBeEmpty()
				->end()
				->scalarNode('password')
					->isRequired()
					->cannotBeEmpty()
				->end()
				->scalarNode('sender')
					->defaultNull()
				->end()
				->booleanNode('flash')
					->defaultFalse()
				->end()
			->end();
	}
}

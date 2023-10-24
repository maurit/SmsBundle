<?php

namespace Maurit\Bundle\SmsBundle\DependencyInjection;


use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\ProviderFactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;


/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration
	implements ConfigurationInterface
{
	/** @var ProviderFactoryInterface[] */
	private $providerFactoryMap = [];


	public function __construct(array $providerFactoryMap)
	{
		$this->providerFactoryMap = $providerFactoryMap;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getConfigTreeBuilder(): TreeBuilder
	{
		$treeBuilder = new TreeBuilder('maurit_sms');

		$this->buildProviderConfiguration($treeBuilder->getRootNode());

		return $treeBuilder;
	}

	private function buildProviderConfiguration(ArrayNodeDefinition $nodeDefinition): void
	{
		$nd = $nodeDefinition
			->fixXmlConfig('provider', 'providers')
			->children()
				->arrayNode('providers')
					->arrayPrototype()
						->performNoDeepMerging();

		foreach ($this->providerFactoryMap as $providerName => $providerFactory) {
			$providerFactory->buildConfiguration(
				$nd->children()->arrayNode($providerName)
			);
		}
	}
}

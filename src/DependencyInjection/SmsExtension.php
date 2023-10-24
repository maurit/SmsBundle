<?php

namespace Maurit\Bundle\SmsBundle\DependencyInjection;


use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\ProviderFactoryInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


class SmsExtension
	extends Extension
{
	/** @var ProviderFactoryInterface[] */
	private $providerFactoryMap = [];


	public function addProviderFactory(ProviderFactoryInterface $providerFactory): void
	{
		$this->providerFactoryMap[$providerFactory->getName()] = $providerFactory;
	}

	public function getAlias(): string
	{
		return 'maurit_sms';
	}

	/**
	 * {@inheritdoc}
	 */
	public function load(array $configs, ContainerBuilder $container): void
	{
		$config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

		// load bundle's services
		$loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
		$loader->load('services.yml');

		// setting up configuration
		$this->loadProviders($config['providers'], $container);
	}

	public function getConfiguration(array $config, ContainerBuilder $container): Configuration
	{
		return new Configuration($this->providerFactoryMap);
	}

	private function loadProviders(array $config, ContainerBuilder $container): void
	{
		foreach ($config as $providerName => $providerConfig) {
			$factoryName = key($providerConfig);
			$factory = $this->providerFactoryMap[$factoryName];
			$definition = $factory->getDefinition($providerConfig[$factoryName]);

			$factory->setProviderDefinition($container, $providerName, $definition);
		}
	}
}

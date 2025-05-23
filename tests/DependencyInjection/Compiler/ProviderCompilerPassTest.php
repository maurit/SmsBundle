<?php
declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\Tests\DependencyInjection\Compiler;

use Maurit\Bundle\SmsBundle\DependencyInjection\Compiler\ProviderCompilerPass;
use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\AbstractProviderFactory;
use Maurit\Bundle\SmsBundle\Provider\EurosmsComProvider;
use Maurit\Bundle\SmsBundle\Provider\SmsAeroProvider;
use Maurit\Bundle\SmsBundle\Provider\SmsBranaSkProvider;
use Maurit\Bundle\SmsBundle\Provider\SmsCenterProvider;
use Maurit\Bundle\SmsBundle\Provider\SmsDiscountProvider;
use Maurit\Bundle\SmsBundle\Provider\SmsgateSkProvider;
use Maurit\Bundle\SmsBundle\Provider\SmsRuProvider;
use Maurit\Bundle\SmsBundle\Service\ProviderManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;


class ProviderCompilerPassTest
	extends TestCase
{
	public function testThatCompilerPassProcessedProviders(): void
	{
		$container = new ContainerBuilder;

		$container->addDefinitions([
			ProviderManager::class => (new Definition(ProviderManager::class)),
			SmsRuProvider::class => $this->getProviderDefinition(SmsRuProvider::class),
			SmsCenterProvider::class => $this->getProviderDefinition(SmsCenterProvider::class),
			SmsDiscountProvider::class => $this->getProviderDefinition(SmsDiscountProvider::class),
			SmsAeroProvider::class => $this->getProviderDefinition(SmsAeroProvider::class),
			EurosmsComProvider::class => $this->getProviderDefinition(EurosmsComProvider::class),
			SmsBranaSkProvider::class => $this->getProviderDefinition(SmsBranaSkProvider::class),
			SmsgateSkProvider::class => $this->getProviderDefinition(SmsgateSkProvider::class)
		]);

		(new ProviderCompilerPass)->process($container);

		$service = $container->get(ProviderManager::class);

		self::assertInstanceOf(SmsRuProvider::class, $service->getProvider(SmsRuProvider::class));
		self::assertInstanceOf(SmsCenterProvider::class, $service->getProvider(SmsCenterProvider::class));
		self::assertInstanceOf(SmsDiscountProvider::class, $service->getProvider(SmsDiscountProvider::class));
		self::assertInstanceOf(SmsAeroProvider::class, $service->getProvider(SmsAeroProvider::class));
		self::assertInstanceOf(EurosmsComProvider::class, $service->getProvider(EurosmsComProvider::class));
		self::assertInstanceOf(SmsBranaSkProvider::class, $service->getProvider(SmsBranaSkProvider::class));
		self::assertInstanceOf(SmsgateSkProvider::class, $service->getProvider(SmsgateSkProvider::class));
	}

	protected function getProviderDefinition(string $class): Definition
	{
		return (new Definition($class))
			->addTag(AbstractProviderFactory::SERVICE_TAG, ['provider' => $class]);
	}
}

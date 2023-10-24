<?php

namespace Maurit\Bundle\SmsBundle;


use Maurit\Bundle\SmsBundle\DependencyInjection\Compiler\ProviderCompilerPass;
use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\MessageBirdProviderFactory;
use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\SmsAeroProviderFactory;
use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\SmsCenterProviderFactory;
use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\SmsDiscountProviderFactory;
use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\SmsRuProviderFactory;
use Maurit\Bundle\SmsBundle\DependencyInjection\SmsExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider\EuroSmsProviderFactory;


class SmsBundle
	extends Bundle
{
	public function build(ContainerBuilder $container): void
	{
		parent::build($container);

		$container->addCompilerPass(new ProviderCompilerPass);
	}

	public function getContainerExtension(): SmsExtension
	{
		$extension = new SmsExtension;
		$extension->addProviderFactory(new MessageBirdProviderFactory);
		$extension->addProviderFactory(new SmsRuProviderFactory);
		$extension->addProviderFactory(new SmsAeroProviderFactory);
		$extension->addProviderFactory(new SmsDiscountProviderFactory);
		$extension->addProviderFactory(new SmsCenterProviderFactory);
		$extension->addProviderFactory(new EuroSmsProviderFactory);

		return $extension;
	}
}

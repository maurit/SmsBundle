<?php declare(strict_types=1);

namespace Maurit\Bundle\SmsBundle\DependencyInjection\Factory\Provider;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;


class SmsBranaSkProviderFactory
    extends AbstractProviderFactory
{
    public function getName(): string
    {
        return 'sms_brana_sk';
    }

    public function getDefinition(array $config): ChildDefinition
    {
        return (new ChildDefinition('maurit_sms.prototype.provider.sms_brana_sk'))
            ->addMethodCall('setLogin', [$config['login']])
            ->addMethodCall('setPassword', [$config['password']])
            ;
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
            ->end();
    }
}

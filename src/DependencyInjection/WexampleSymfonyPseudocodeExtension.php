<?php

namespace Wexample\SymfonyPseudocode\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wexample\SymfonyHelpers\DependencyInjection\AbstractWexampleSymfonyExtension;

class WexampleSymfonyPseudocodeExtension extends AbstractWexampleSymfonyExtension
{
    public function load(
        array $configs,
        ContainerBuilder $container
    ): void {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            'wexample_symfony_pseudocode.paths',
            $config['paths']
        );
        $container->setParameter(
            'wexample_symfony_pseudocode.output_dir',
            $config['output_dir']
        );

        $this->loadConfig(
            __DIR__,
            $container
        );
    }
}

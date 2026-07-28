<?php

namespace Wexample\SymfonyPseudocode\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wexample\Helpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\DependencyInjection\AbstractWexampleSymfonyExtension;
use Wexample\SymfonyPseudocode\Interface\PseudocodeBundleInterface;

class WexampleSymfonyPseudocodeExtension extends AbstractWexampleSymfonyExtension
{
    public function load(
        array $configs,
        ContainerBuilder $container
    ): void {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            'wexample_symfony_pseudocode.output_dir',
            $config['output_dir']
        );

        $bundleSources = [];
        foreach ($container->getParameter('kernel.bundles') as $class) {
            if (ClassHelper::classImplementsInterface($class, PseudocodeBundleInterface::class)) {
                foreach ($class::getPseudocodeSourcePaths() as $path) {
                    $bundleSources[] = $path;
                }
            }
        }

        $container->setParameter(
            'wexample_symfony_pseudocode.additional_sources',
            array_merge($bundleSources, $config['additional_sources'])
        );

        $this->loadConfig(
            __DIR__,
            $container
        );
    }
}

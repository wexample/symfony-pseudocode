<?php

namespace Wexample\SymfonyPseudocode\Command;

use Symfony\Component\Console\Input\InputArgument;
use Wexample\SymfonyHelpers\Command\AbstractBundleCommand;
use Wexample\SymfonyPseudocode\WexampleSymfonyPseudocodeBundle;

abstract class AbstractPseudocodeGenerateCommand extends AbstractBundleCommand
{
    public static function getCommandPrefixGroup(): string
    {
        return 'pseudocode:generate';
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'outputDir',
                InputArgument::OPTIONAL,
                'Directory where to generate the Symfony project',
                'pseudocode'
            );
    }

    public static function getBundleClassName(): string
    {
        return WexampleSymfonyPseudocodeBundle::class;
    }
}

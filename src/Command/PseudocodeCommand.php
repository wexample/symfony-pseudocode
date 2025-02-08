<?php

namespace Wexample\SymfonyPseudocode\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use Wexample\SymfonyHelpers\Service\BundleService;

class PseudocodeCommand extends AbstractPseudocodeGenerateCommand
{
    public function __construct(
        protected BundleService $bundleService,
        protected KernelInterface $container
    )
    {
        parent::__construct($bundleService);
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int
    {
        $projectDir = $this->container->getProjectDir();
        $entityDir = $projectDir . '/src/Entity';

        $finder = new Finder();
        $finder->files()
            ->in($entityDir)
            ->name('*.php');

        if (!$finder->hasResults()) {
            $output->writeln('No entity found in ' . $entityDir);
            return Command::SUCCESS;
        }

        foreach ($finder as $file) {
            $relativePath = $file->getRelativePathname();
            $output->writeln('Entity found: ' . $relativePath);
        }

        return Command::SUCCESS;
    }
}

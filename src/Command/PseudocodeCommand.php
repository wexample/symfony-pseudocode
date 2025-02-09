<?php

namespace Wexample\SymfonyPseudocode\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Wexample\SymfonyHelpers\Service\BundleService;
use Wexample\SymfonyPseudocode\Service\PseudocodeService;

class PseudocodeCommand extends AbstractPseudocodeGenerateCommand
{
    protected PseudocodeService $pseudocodeService;

    public function __construct(
        protected BundleService $bundleService,
        protected KernelInterface $kernel
    )
    {
        parent::__construct($bundleService);
        $this->pseudocodeService = new PseudocodeService($kernel);
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int
    {
        $this->pseudocodeService->process(
            $this->kernel->getProjectDir() . '/' . $input->getArgument('pseudocode-dir') . '/'
        );

        return Command::SUCCESS;
    }
}

<?php

namespace Wexample\SymfonyPseudocode\Service;

use Symfony\Component\HttpKernel\KernelInterface;
use Wexample\SymfonyHelpers\Service\BundleService;
use Wexample\SymfonyPseudocode\Processor\EntityProcessor;
use Wexample\SymfonyPseudocode\Processor\RepositoryProcessor;

class PseudocodeService
{
    protected EntityProcessor $entityProcessor;
    protected RepositoryProcessor $repositoryProcessor;

    public function __construct(
        protected KernelInterface $kernel,
        protected BundleService $bundleService
    ) {
        $this->entityProcessor = new EntityProcessor($kernel);
        $this->repositoryProcessor = new RepositoryProcessor($kernel);
    }

    public function process(): void
    {
        $this->entityProcessor->process();
        $this->repositoryProcessor->process();
    }
}

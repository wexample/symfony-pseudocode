<?php

namespace Wexample\SymfonyPseudocode\Service;

use Symfony\Component\HttpKernel\KernelInterface;
use Wexample\Pseudocode\Generator\PseudocodeGenerator;
use Wexample\SymfonyHelpers\Service\BundleService;
use Wexample\SymfonyPseudocode\Processor\EntityProcessor;
use Wexample\SymfonyPseudocode\Processor\RepositoryProcessor;

class PseudocodeService
{
    protected EntityProcessor $entityProcessor;
    protected RepositoryProcessor $repositoryProcessor;

    public function __construct(
        protected KernelInterface $kernel,
        protected BundleService $bundleService,
    )
    {
        $pseudocodeGenerator = new PseudocodeGenerator();
        $this->entityProcessor = new EntityProcessor($kernel, $pseudocodeGenerator);
        $this->repositoryProcessor = new RepositoryProcessor($kernel, $pseudocodeGenerator);
    }

    public function process(string $pseudocodeDir): void
    {
        $this->entityProcessor->process($pseudocodeDir);
        $this->repositoryProcessor->process($pseudocodeDir);
    }
}

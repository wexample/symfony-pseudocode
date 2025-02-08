<?php

namespace Wexample\SymfonyPseudocode\Service;

use Symfony\Component\HttpKernel\KernelInterface;
use Wexample\Pseudocode\Generator\PseudocodeGenerator;
use Wexample\SymfonyPseudocode\Processor\EntityProcessor;
use Wexample\SymfonyPseudocode\Processor\RepositoryProcessor;

class PseudocodeService
{
    protected EntityProcessor $entityProcessor;
    protected RepositoryProcessor $repositoryProcessor;

    public function __construct(
        protected KernelInterface $kernel,
    )
    {
        $pseudocodeGenerator = new PseudocodeGenerator();
        $this->entityProcessor = new EntityProcessor($pseudocodeGenerator);
        $this->repositoryProcessor = new RepositoryProcessor($pseudocodeGenerator);
    }

    /**
     * @param string $pseudocodeDir Directory where to generate pseudocode
     * @param string|null $codeDir Optional directory containing the source code, defaults to kernel project dir
     * @return string[]
     */
    public function process(string $pseudocodeDir, ?string $codeDir = null): array
    {
        $codeDir = $codeDir ?? $this->kernel->getProjectDir() . '/src';
        
        $files = [];
        $files = array_merge($files, $this->entityProcessor->process($codeDir, $pseudocodeDir));
        $files = array_merge($files, $this->repositoryProcessor->process($codeDir, $pseudocodeDir));

        return $files;
    }
}

<?php

namespace Wexample\SymfonyPseudocode\Service;

use Symfony\Component\HttpKernel\KernelInterface;
use Wexample\SymfonyPseudocode\Generator\SymfonyCodeGenerator;
use Wexample\SymfonyPseudocode\Generator\SymfonyPseudocodeGenerator;
use Wexample\SymfonyPseudocode\Processor\AbstractProcessor;
use Wexample\SymfonyPseudocode\Processor\EntityProcessor;
use Wexample\SymfonyPseudocode\Processor\RepositoryProcessor;

class PseudocodeService
{

    protected EntityProcessor $entityProcessor;
    protected RepositoryProcessor $repositoryProcessor;
    /** @var AbstractProcessor[] */
    protected $processors = [];

    public function __construct(
        protected KernelInterface $kernel,
    )
    {
        $pseudocodeGenerator = new SymfonyPseudocodeGenerator();
        $codeGenerator = new SymfonyCodeGenerator();

        $processors = [
            EntityProcessor::class,
            RepositoryProcessor::class,
        ];

        foreach ($processors as $processor) {
            $this->processors[$processor] = new $processor(
                $pseudocodeGenerator,
                $codeGenerator,
            );
        }
    }

    /**
     * @param string $pseudocodeDir Directory where to generate pseudocode
     * @param string|null $codeDir Optional directory containing the source code, defaults to kernel project dir
     * @return string[]
     */
    public function process(
        string $pseudocodeDir,
        ?string $codeDir = null
    ): array
    {
        $codeDir = $codeDir ?? $this->kernel->getProjectDir() . '/src';

        $files = [];
        foreach ($this->processors as $processor) {
            $files = array_merge($processor->process(
                $codeDir,
                $pseudocodeDir
            ), $files);
        }

        return $files;
    }
}

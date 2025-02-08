<?php

namespace Wexample\SymfonyPseudocode\Processor;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use Wexample\Pseudocode\Generator\PseudocodeGenerator;

abstract class AbstractFileProcessor
{
    public function __construct(
        protected KernelInterface $kernel,
        protected PseudocodeGenerator $pseudocodeGenerator
    )
    {
    }

    abstract protected function getSourceDirectory(): string;

    protected function getFilePattern(): string
    {
        return '*.php';
    }

    /**
     * @param string $pseudocodeRootDir
     * @return string[]
     */
    public function process(string $pseudocodeRootDir): array
    {
        $finder = new Finder();
        $finder->files()
            ->in($this->getSourceDirectory())
            ->name($this->getFilePattern());

        $files = [];
        $projectDir = $this->kernel->getProjectDir();
        foreach ($finder as $file) {
            $files[] = $this->pseudocodeGenerator->generateFromFileAndSave(
                $file,
                $projectDir . '/',
                $pseudocodeRootDir,
            );
        }

        return $files;
    }

    abstract protected function getProcessorName(): string;
}

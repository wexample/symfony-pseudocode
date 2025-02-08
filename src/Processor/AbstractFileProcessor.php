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

    public function process(string $pseudocodeRootDir): void
    {
        $finder = new Finder();
        $finder->files()
            ->in($this->getSourceDirectory())
            ->name($this->getFilePattern());

        if (!$finder->hasResults()) {
            return;
        }

        $projectDir = $this->kernel->getProjectDir();
        foreach ($finder as $file) {
            $this->pseudocodeGenerator->generateFromFileAndSave(
                $file,
                $projectDir . '/',
                $pseudocodeRootDir,
            );
        }
    }

    abstract protected function getProcessorName(): string;
}

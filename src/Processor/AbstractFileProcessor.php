<?php

namespace Wexample\SymfonyPseudocode\Processor;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractFileProcessor
{
    public function __construct(
        protected KernelInterface $kernel
    ) {
    }

    abstract protected function getSourceDirectory(): string;

    protected function getFilePattern(): string
    {
        return '*.php';
    }

    public function process(): void
    {
        $finder = new Finder();
        $finder->files()
            ->in($this->getSourceDirectory())
            ->name($this->getFilePattern());

        if (!$finder->hasResults()) {
            return;
        }

        foreach ($finder as $file) {
            $this->processFile($file);
        }
    }

    protected function processFile($file): void
    {
        echo sprintf(
            "Processing %s file: %s\n",
            $this->getProcessorName(),
            $file->getRelativePathname()
        );
    }

    abstract protected function getProcessorName(): string;
}

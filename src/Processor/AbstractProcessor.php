<?php

namespace Wexample\SymfonyPseudocode\Processor;

use Symfony\Component\Finder\Finder;
use Wexample\SymfonyPseudocode\Generator\SymfonyCodeGenerator;
use Wexample\SymfonyPseudocode\Generator\SymfonyPseudocodeGenerator;

abstract class AbstractProcessor
{
    public function __construct(
        protected SymfonyPseudocodeGenerator $pseudocodeGenerator,
        protected SymfonyCodeGenerator $codeGenerator,
    )
    {
    }

    abstract protected function getSourceSubDirectory(): string;

    protected function getFilePattern(): string
    {
        return '*.php';
    }

    /**
     * @param string $codeDir Directory containing the source code
     * @param string $pseudocodeRootDir Directory where to generate pseudocode
     * @return string[]
     */
    public function process(
        string $codeDir,
        string $pseudocodeRootDir
    ): array
    {
        $sourceDir = $codeDir . '/' . $this->getSourceSubDirectory();

        $finder = new Finder();
        $finder->files()
            ->in($sourceDir)
            ->name($this->getFilePattern());

        $files = [];
        foreach ($finder as $file) {
            $files[] = $this->pseudocodeGenerator->generateFromFileAndSave(
                $file,
                $codeDir . '/',
                $pseudocodeRootDir,
            );
        }

        return $files;
    }

    abstract protected function getProcessorName(): string;
}

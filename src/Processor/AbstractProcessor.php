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
    ) {
    }

    abstract protected function getSourceSubDirectory(): string;

    protected function getFilePattern(): string
    {
        return '*.php';
    }

    /**
     * @param string $codeDir Directory containing the source code
     * @param string $pseudocodeRootDir Directory where to generate pseudocode
     * @param bool $recursive Process directories recursively
     * @return array{scanned: int, generated: string[]}
     */
    public function process(
        string $codeDir,
        string $pseudocodeRootDir,
        bool $recursive = false
    ): array {
        $sourceDir = $codeDir . '/' . $this->getSourceSubDirectory();

        $finder = new Finder();
        $finder->files()
            ->in($sourceDir)
            ->name($this->getFilePattern());
        if (! $recursive) {
            $finder->depth('== 0');
        }

        $files = [];
        $scanned = 0;
        foreach ($finder as $file) {
            $scanned++;
            $outputFile = $this->pseudocodeGenerator->generateFromFileAndSave(
                $file,
                $codeDir . '/',
                $pseudocodeRootDir,
            );
            if ($outputFile) {
                $files[] = $outputFile;
            }
        }

        return [
            'scanned' => $scanned,
            'generated' => $files,
        ];
    }

    abstract protected function getProcessorName(): string;
}

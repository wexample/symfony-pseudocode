<?php

namespace Wexample\SymfonyPseudocode\Service;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use Wexample\Pseudocode\Parser\ClassIndex;
use Wexample\Pseudocode\Parser\ParserContext;
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
    protected ?ClassIndex $classIndex = null;

    protected SymfonyPseudocodeGenerator $pseudocodeGenerator;
    protected SymfonyCodeGenerator $codeGenerator;

    public function __construct(
        protected KernelInterface $kernel,
    ) {
        $this->pseudocodeGenerator = new SymfonyPseudocodeGenerator();
        $this->classIndex = $this->buildClassIndex();
        $this->pseudocodeGenerator->setParserContext(
            new ParserContext($this->classIndex)
        );
        $this->codeGenerator = new SymfonyCodeGenerator();

        $processors = [
            EntityProcessor::class,
            RepositoryProcessor::class,
        ];

        foreach ($processors as $processor) {
            $this->processors[$processor] = new $processor(
                $this->pseudocodeGenerator,
                $this->codeGenerator,
            );
        }
    }

    protected function buildClassIndex(): ClassIndex
    {
        $index = new ClassIndex();
        $projectDir = $this->kernel->getProjectDir();
        $sourceDir = $projectDir . '/src';
        if (is_dir($sourceDir)) {
            $finder = new Finder();
            $finder->files()
                ->in($sourceDir)
                ->name('*.php');

            foreach ($finder as $file) {
                $index->addFile($file->getPathname());
            }
        }

        return $index;
    }

    protected function resolvePath(string $projectDir, string $path): string
    {
        if (str_starts_with($path, '/')) {
            return $path;
        }

        return rtrim($projectDir, '/') . '/' . ltrim($path, '/');
    }

    /**
     * @param string $pseudocodeDir Directory where to generate pseudocode
     * @param string $sourcePath Path to a specific file or directory to process
     * @param bool $recursive Process directories recursively
     * @return array{scanned: int, generated: string[]}
     */
    public function process(
        string $pseudocodeDir,
        string $sourcePath,
        bool $recursive = false
    ): array {
        // Ensure pseudocode directory ends with a slash
        if (! str_ends_with($pseudocodeDir, '/')) {
            $pseudocodeDir .= '/';
        }

        $files = [];
        $scanned = 0;

        // If source path is a file, process it directly
        if (is_file($sourcePath)) {
            return $this->processFile($sourcePath, $pseudocodeDir);
        }

        // If source path is a directory, process it with processors
        if (is_dir($sourcePath)) {
            // Process with each processor
            foreach ($this->processors as $processor) {
                $processorResult = $processor->process(
                    $sourcePath,
                    $pseudocodeDir,
                    $recursive
                );
                $scanned += $processorResult['scanned'];
                $files = array_merge($files, $processorResult['generated']);
            }
        }

        return [
            'scanned' => $scanned,
            'generated' => $files,
        ];
    }

    /**
     * Process a single file
     *
     * @param string $filePath Path to the file
     * @param string $pseudocodeDir Directory where to generate pseudocode
     * @return array{scanned: int, generated: string[]}
     */
    protected function processFile(string $filePath, string $pseudocodeDir): array
    {
        // Get the project root directory to determine relative paths
        $projectDir = $this->kernel->getProjectDir();

        // Make sure the file path is absolute
        if (! str_starts_with($filePath, '/')) {
            $filePath = $projectDir . '/' . $filePath;
        }

        // Only process PHP files
        if (! str_ends_with($filePath, '.php')) {
            return [
                'scanned' => 0,
                'generated' => [],
            ];
        }

        // Generate pseudocode for the file
        $outputFile = $this->pseudocodeGenerator->generateFromFileAndSave(
            new \SplFileInfo($filePath),
            $projectDir . '/',
            $pseudocodeDir
        );

        return [
            'scanned' => 1,
            'generated' => $outputFile ? [$outputFile] : [],
        ];
    }

    // processDirectory removed: processors handle subdirectories and recursion
}

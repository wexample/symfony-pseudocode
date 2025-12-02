<?php

namespace Wexample\SymfonyPseudocode\Service;

use Symfony\Component\Finder\Finder;
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

    protected SymfonyPseudocodeGenerator $pseudocodeGenerator;
    protected SymfonyCodeGenerator $codeGenerator;

    public function __construct(
        protected KernelInterface $kernel,
    ) {
        $this->pseudocodeGenerator = new SymfonyPseudocodeGenerator();
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

    /**
     * @param string $pseudocodeDir Directory where to generate pseudocode
     * @param string $sourcePath Path to a specific file or directory to process
     * @param bool $recursive Process directories recursively
     * @return string[] List of processed files
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

        // If source path is a file, process it directly
        if (is_file($sourcePath)) {
            return $this->processFile($sourcePath, $pseudocodeDir);
        }

        // If source path is a directory, process it with processors
        if (is_dir($sourcePath)) {
            // Process with each processor
            foreach ($this->processors as $processor) {
                $processorFiles = $this->processDirectory(
                    $sourcePath,
                    $pseudocodeDir,
                    $processor,
                    $recursive
                );
                $files = array_merge($files, $processorFiles);
            }
        }

        return $files;
    }

    /**
     * Process a single file
     *
     * @param string $filePath Path to the file
     * @param string $pseudocodeDir Directory where to generate pseudocode
     * @return string[] List containing the processed file
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
            return [];
        }

        // Generate pseudocode for the file
        $outputFile = $this->pseudocodeGenerator->generateFromFileAndSave(
            new \SplFileInfo($filePath),
            $projectDir . '/',
            $pseudocodeDir
        );

        return [$outputFile];
    }

    /**
     * Process a directory using a specific processor
     *
     * @param string $sourceDir Directory to process
     * @param string $pseudocodeDir Directory where to generate pseudocode
     * @param AbstractProcessor $processor Processor to use
     * @param bool $recursive Process directories recursively
     * @return string[] List of processed files
     */
    protected function processDirectory(
        string $sourceDir,
        string $pseudocodeDir,
        AbstractProcessor $processor,
        bool $recursive = false
    ): array {
        // Create a finder to locate files
        $finder = new Finder();
        $finder->files()
            ->in($sourceDir)
            ->name('*.php');

        // Set recursion option
        if (! $recursive) {
            $finder->depth('== 0');
        }

        $files = [];
        foreach ($finder as $file) {
            // Generate pseudocode for each file
            $outputFile = $this->pseudocodeGenerator->generateFromFileAndSave(
                $file,
                $this->kernel->getProjectDir() . '/',
                $pseudocodeDir
            );

            if ($outputFile) {
                $files[] = $outputFile;
            }
        }

        return $files;
    }
}

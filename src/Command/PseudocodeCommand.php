<?php

namespace Wexample\SymfonyPseudocode\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Wexample\SymfonyHelpers\Service\BundleService;
use Wexample\SymfonyPseudocode\Service\PseudocodeService;

class PseudocodeCommand extends AbstractPseudocodeGenerateCommand
{
    public function __construct(
        protected BundleService $bundleService,
        protected KernelInterface $kernel,
        protected PseudocodeService $pseudocodeService,
        protected string $defaultOutputDir
    ) {
        parent::__construct($bundleService);
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->addArgument(
                'source-path',
                InputArgument::OPTIONAL,
                'Specific file or directory to process (relative to project root)',
                null
            )
            ->addOption(
                'recursive',
                'r',
                InputOption::VALUE_NONE,
                'Process directories recursively'
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $projectDir = $this->kernel->getProjectDir();
        $sourcePath = $input->getArgument('source-path');
        $pseudocodeDir = $input->getArgument('pseudocode-dir')
            ?? $this->defaultOutputDir
            ?? 'pseudocode';
        $recursive = $input->getOption('recursive');

        // Make sure paths are absolute
        $pseudocodeDir = $this->resolvePath($projectDir, $pseudocodeDir);

        $output->writeln(sprintf('Output directory: %s', $pseudocodeDir));

        $sourcePath = $sourcePath ?? 'src';
        $sourcePath = $this->resolvePath($projectDir, $sourcePath);
        $output->writeln(sprintf('Processing source: %s', $sourcePath));
        $result = $this->pseudocodeService->process(
            $pseudocodeDir,
            $sourcePath,
            $recursive
        );

        $output->writeln(sprintf('Scanned %d files', $result['scanned']));

        if (empty($result['generated'])) {
            $output->writeln('Generated 0 pseudocode files');
        } else {
            $output->writeln(sprintf('Generated %d pseudocode files:', count($result['generated'])));
            foreach ($result['generated'] as $file) {
                $output->writeln(' - ' . $file);
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Resolve a path to an absolute path
     */
    private function resolvePath(string $baseDir, string $path): string
    {
        // If path is already absolute, return it
        if (strpos($path, '/') === 0) {
            return $path;
        }

        // Otherwise, make it absolute
        return $baseDir . '/' . $path;
    }
}

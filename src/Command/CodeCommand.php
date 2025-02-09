<?php

namespace Wexample\SymfonyPseudocode\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CodeCommand extends AbstractPseudocodeGenerateCommand
{
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int
    {
        $pseudocodeDir = $input->getArgument('pseudocode-dir');

        $output->writeln($pseudocodeDir);

        // TODO: Generate code from the pseudocode dir.
        // - Explore the Entity directory to find entities
        // - Fill the outputDir with generated pseudocode based on found files.

        $output->writeln('TODO: Implement project conversion to pseudocode');

        return Command::SUCCESS;
    }
}

<?php

namespace Wexample\SymfonyPseudocode\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PseudocodeCommand extends AbstractPseudocodeGenerateCommand
{
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int
    {
        $pseudocodeDir = $input->getArgument('pseudocode-dir');

        // TODO: Generate pseudocode from the symfony project.
        // - Explore the Entity directory to find entities
        // - Fill the outputDir with generated pseudocode based on found files.

        $output->writeln('TODO: Implement pseudocode to Symfony project conversion');

        return Command::SUCCESS;
    }
}

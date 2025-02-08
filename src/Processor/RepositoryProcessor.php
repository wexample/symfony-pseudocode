<?php

namespace Wexample\SymfonyPseudocode\Processor;

class RepositoryProcessor extends AbstractFileProcessor
{
    protected function getSourceDirectory(): string
    {
        return $this->kernel->getProjectDir() . '/src/Repository';
    }

    protected function getProcessorName(): string
    {
        return 'Repository';
    }
}

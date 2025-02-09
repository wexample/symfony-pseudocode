<?php

namespace Wexample\SymfonyPseudocode\Processor;

class RepositoryProcessor extends AbstractFileProcessor
{
    protected function getSourceSubDirectory(): string
    {
        return 'Repository';
    }

    protected function getProcessorName(): string
    {
        return 'Repository';
    }
}

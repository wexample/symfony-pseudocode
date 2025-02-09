<?php

namespace Wexample\SymfonyPseudocode\Processor;

class RepositoryProcessor extends AbstractProcessor
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

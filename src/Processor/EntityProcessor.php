<?php

namespace Wexample\SymfonyPseudocode\Processor;

class EntityProcessor extends AbstractFileProcessor
{
    protected function getSourceSubDirectory(): string
    {
        return 'Entity';
    }

    protected function getProcessorName(): string
    {
        return 'Entity';
    }
}

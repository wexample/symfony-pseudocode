<?php

namespace Wexample\SymfonyPseudocode\Processor;

class EntityProcessor extends AbstractFileProcessor
{
    protected function getSourceDirectory(): string
    {
        return $this->kernel->getProjectDir() . '/src/Entity';
    }

    protected function getProcessorName(): string
    {
        return 'Entity';
    }
}

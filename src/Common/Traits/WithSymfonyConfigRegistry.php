<?php

namespace Wexample\SymfonyPseudocode\Common\Traits;

use Wexample\Pseudocode\Common\Traits\WithConfigRegistry;
use Wexample\SymfonyPseudocode\Common\SymfonyConfigRegistry;

trait WithSymfonyConfigRegistry
{
    use WithConfigRegistry;

    protected function getConfigRegistryClass(): string
    {
        return SymfonyConfigRegistry::class;
    }
}

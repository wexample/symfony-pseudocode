<?php

namespace Wexample\SymfonyPseudocode\Tests\Processor;

use PHPUnit\Framework\TestCase;
use Wexample\SymfonyPseudocode\Processor\RepositoryProcessor;

class RepositoryProcessorTest extends TestCase
{
    private RepositoryProcessor $processor;

    protected function setUp(): void
    {
        $this->processor = new RepositoryProcessor();
    }

    public function testProcessRepository(): void
    {
        // TODO
        $this->assertTrue(true);
    }
}

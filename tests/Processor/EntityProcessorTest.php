<?php

namespace Wexample\SymfonyPseudocode\Tests\Processor;

use PHPUnit\Framework\TestCase;
use Wexample\SymfonyPseudocode\Processor\EntityProcessor;

class EntityProcessorTest extends TestCase
{
    private EntityProcessor $processor;

    protected function setUp(): void
    {
        $this->processor = new EntityProcessor();
    }

    public function testProcessEntity(): void
    {
        // TODO
        $this->assertTrue(true);
    }
}

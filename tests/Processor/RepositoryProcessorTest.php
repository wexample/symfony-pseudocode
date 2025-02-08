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
        $sourceFile = __DIR__ . '/../Fixtures/Repository/TestEntityRepository.php';
        $content = file_get_contents($sourceFile);

        $result = $this->processor->process($content);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('repository', $result);
        $this->assertEquals('TestEntityRepository', $result['repository']['name']);
        $this->assertArrayHasKey('methods', $result['repository']);

        // Test methods
        $methods = $result['repository']['methods'];
        $this->assertArrayHasKey('findByName', $methods);

        // Test findByName method
        $findByName = $methods['findByName'];
        $this->assertArrayHasKey('parameters', $findByName);
        $this->assertArrayHasKey('name', $findByName['parameters']);
        $this->assertEquals('string', $findByName['parameters']['name']['type']);
    }
}

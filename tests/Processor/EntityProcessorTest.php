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
        $sourceFile = __DIR__ . '/../Fixtures/Entity/TestEntity.php';
        $content = file_get_contents($sourceFile);

        $result = $this->processor->process($content);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('entity', $result);
        $this->assertEquals('TestEntity', $result['entity']['name']);
        $this->assertArrayHasKey('properties', $result['entity']);
        $this->assertArrayHasKey('methods', $result['entity']);

        // Test properties
        $properties = $result['entity']['properties'];
        $this->assertArrayHasKey('id', $properties);
        $this->assertArrayHasKey('name', $properties);

        // Test id property
        $this->assertEquals('int', $properties['id']['type']);
        $this->assertTrue($properties['id']['nullable']);
        $this->assertTrue($properties['id']['generated']);
        $this->assertTrue($properties['id']['primary']);

        // Test name property
        $this->assertEquals('string', $properties['name']['type']);
        $this->assertTrue($properties['name']['nullable']);
        $this->assertEquals(255, $properties['name']['length']);

        // Test methods
        $methods = $result['entity']['methods'];
        $this->assertArrayHasKey('getId', $methods);
        $this->assertArrayHasKey('getName', $methods);
        $this->assertArrayHasKey('setName', $methods);

        // Test getId method
        $this->assertEquals('?int', $methods['getId']['return_type']);

        // Test setName method
        $this->assertArrayHasKey('parameters', $methods['setName']);
        $this->assertArrayHasKey('name', $methods['setName']['parameters']);
        $this->assertEquals('string', $methods['setName']['parameters']['name']['type']);
    }
}

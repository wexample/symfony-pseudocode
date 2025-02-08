<?php

namespace Wexample\SymfonyPseudocode\Tests\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Wexample\SymfonyPseudocode\Service\PseudocodeService;

class PseudocodeServiceTest extends TestCase
{
    private ?PseudocodeService $pseudocodeService = null;
    private string $tempTestDir;
    private string $fixturesDir;
    private string $sourceDir;

    protected function setUp(): void
    {
        $this->fixturesDir = __DIR__ . '/../Fixtures';
        $this->sourceDir = $this->fixturesDir . '/Classes';
        $this->tempTestDir = sys_get_temp_dir() . '/pseudocode_test_' . uniqid();

        // Create test directory
        (new Filesystem())->mkdir($this->tempTestDir);

        // Mock kernel - we don't need getProjectDir anymore but service still requires it
        $kernel = $this->createMock(KernelInterface::class);
        $this->pseudocodeService = new PseudocodeService($kernel);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempTestDir)) {
            (new Filesystem())->remove($this->tempTestDir);
        }
    }

    public function testEntityConversion(): void
    {
        // Process the test entities and repositories
        $files = $this->pseudocodeService->process($this->tempTestDir, $this->sourceDir);
        
        $this->assertNotEmpty($files, 'The service should have produced files.');
        
        // Verify the generated files match expected output
        foreach ($files as $file) {
            $relativePath = basename($file);
            $expectedFile = $this->fixturesDir . '/expected/' . $relativePath;
            
            $this->assertFileExists($expectedFile, 'Expected file should exist: ' . $relativePath);
            $this->assertFileEquals(
                $expectedFile,
                $file,
                'Generated file should match expected content: ' . $relativePath
            );
        }
    }
}

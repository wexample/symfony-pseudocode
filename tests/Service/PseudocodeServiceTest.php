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

    protected function setUp(): void
    {
        $kernel = $this->createMock(KernelInterface::class);
        $kernel->method('getProjectDir')
            ->willReturn(__DIR__ . '/../Fixtures/Classes');

        $this->pseudocodeService = new PseudocodeService($kernel);
        
        $this->fixturesDir = __DIR__ . '/../Fixtures';
        $this->tempTestDir = sys_get_temp_dir() . '/pseudocode_test_' . uniqid();
        (new Filesystem())->mkdir($this->tempTestDir);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempTestDir)) {
            (new Filesystem())->remove($this->tempTestDir);
        }
    }

    public function testEntityConversion(): void
    {
        $files = $this->pseudocodeService->process($this->tempTestDir);
        
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

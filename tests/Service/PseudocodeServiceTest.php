<?php

namespace Wexample\SymfonyPseudocode\Tests\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Wexample\Helpers\Testing\Traits\WithYamlTestCase;
use Wexample\SymfonyPseudocode\Service\PseudocodeService;

class PseudocodeServiceTest extends TestCase
{
    use WithYamlTestCase;

    private ?PseudocodeService $pseudocodeService = null;

    private string $tempTestDir;
    private string $fixturesDir;
    private string $sourceDir;
    private string $expectedDir;

    protected function setUp(): void
    {
        $this->fixturesDir = __DIR__ . '/../Fixtures';
        $this->sourceDir = $this->fixturesDir . '/Classes';
        $this->expectedDir = $this->fixturesDir . '/pseudocode';
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
            $type = str_contains(strtolower($relativePath), 'repository') ? 'repository' : 'entity';
            $expectedFile = $this->expectedDir . '/' . $type . '/' . $relativePath;

            $this->assertYamlFilesEqual(
                $expectedFile,
                $file,
                "Generated pseudocode does not match",
                allowEmptyMissing: true
            );
        }
    }
}

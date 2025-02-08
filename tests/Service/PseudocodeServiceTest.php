<?php

namespace Wexample\SymfonyPseudocode\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Wexample\SymfonyPseudocode\Service\PseudocodeService;

class PseudocodeServiceTest extends KernelTestCase
{
    private ?PseudocodeService $pseudocodeService = null;
    private string $tempTestDir;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();
        /** @var PseudocodeService $pseudocodeService */
        $pseudocodeService = $container->get(PseudocodeService::class);
        $this->pseudocodeService = $pseudocodeService;

        $this->tempTestDir = sys_get_temp_dir() . '/pseudocode_test_' . uniqid();
        (new Filesystem())->mkdir($this->tempTestDir);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->tempTestDir);
    }

    public function testEntityConversion(): void
    {
        $files = $this->pseudocodeService->process($this->tempTestDir);

        $this->assertNotEmpty($files, 'The service should have produced files.');
    }
}

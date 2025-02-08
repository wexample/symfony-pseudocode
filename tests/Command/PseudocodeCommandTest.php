<?php

namespace Wexample\SymfonyPseudocode\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
use Wexample\SymfonyHelpers\Service\BundleService;
use Wexample\SymfonyPseudocode\Command\PseudocodeCommand;

class PseudocodeCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private BundleService $bundleService;
    private KernelInterface $kernel;

    protected function setUp(): void
    {
        $this->bundleService = $this->createMock(BundleService::class);
        $this->kernel = $this->createMock(KernelInterface::class);

        $command = new PseudocodeCommand(
            $this->bundleService,
            $this->kernel
        );

        $application = new Application();
        $application->add($command);

        $this->commandTester = new CommandTester($command);
    }

    public function testExecute(): void
    {
        $this->bundleService
            ->method('getBundleRoot')
            ->willReturn(__DIR__ . '/..');

        $outputDir = __DIR__ . '/../Fixtures/generated';
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $this->commandTester->execute([
            'source' => __DIR__ . '/../Fixtures/Entity',
            'destination' => $outputDir
        ]);

        $this->assertFileExists($outputDir . '/test-entity.yml');
        $this->assertFileEquals(
            __DIR__ . '/../Fixtures/expected/test-entity.yml',
            $outputDir . '/test-entity.yml'
        );
    }
}

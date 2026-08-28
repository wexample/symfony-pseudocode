## Architecture

The package is a Symfony bundle wrapped around the framework-agnostic generator from `wexample/php-pseudocode`. Everything it owns lives under `src/`, autoloaded as `Wexample\SymfonyPseudocode\` (PSR-4, declared in composer.json). Roughly: two console commands call one service, the service fans out to processors, and each processor hands files to a generator that comes from the upstream package.

### The bundle and its container extension

src/WexampleSymfonyPseudocodeBundle.php is an empty subclass of `Wexample\SymfonyHelpers\Class\AbstractBundle`; all the wiring happens in src/DependencyInjection/WexampleSymfonyPseudocodeExtension.php, whose `load()` does three things:

1. Publishes `wexample_symfony_pseudocode.output_dir` from the config tree in src/DependencyInjection/Configuration.php (default `pseudocode`, `cannotBeEmpty()`).
2. Walks `kernel.bundles` and collects extra source directories from every registered bundle whose class implements src/Interface/PseudocodeBundleInterface.php:

   ```php
   if (ClassHelper::classImplementsInterface($class, PseudocodeBundleInterface::class)) {
       foreach ($class::getPseudocodeSourcePaths() as $path) {
   ```

   Those paths are merged ahead of the `additional_sources` configured by the application and published as `wexample_symfony_pseudocode.additional_sources`. This is the extension point of the package: a sibling bundle that wants its own classes scanned implements `getPseudocodeSourcePaths(): array` and needs no other change here.
3. Loads src/Resources/config/services.yaml through `AbstractWexampleSymfonyExtension::loadConfig()`.

The service file registers only `Command` and `Service`, and binds the two parameters by argument name:

```yaml
bind:
    $defaultOutputDir: '%wexample_symfony_pseudocode.output_dir%'
    $additionalSources: '%wexample_symfony_pseudocode.additional_sources%'
```

Processors and generators are not services — `PseudocodeService` instantiates them with `new` in its constructor.

### Commands

src/Command/AbstractPseudocodeGenerateCommand.php extends `AbstractBundleCommand` and fixes the group with `getCommandPrefixGroup(): 'pseudocode:generate'`. Command names are derived, not declared: `AbstractCommand::buildDefaultName()` appends the kebab-cased class short name minus the `Command` suffix. So src/Command/PseudocodeCommand.php becomes `pseudocode:generate:pseudocode` and src/Command/CodeCommand.php becomes `pseudocode:generate:code`. Renaming a command class renames the CLI command.

The abstract class contributes the optional `pseudocode-dir` argument. `PseudocodeCommand` adds the optional `source-path` argument and the `--recursive` / `-r` flag, then resolves defaults and delegates:

```php
$pseudocodeDir = $input->getArgument('pseudocode-dir')
    ?? $this->defaultOutputDir
    ?? 'pseudocode';
```

`source-path` defaults to `src`. Both are made absolute against `$this->kernel->getProjectDir()` before the service is called, and the command then only prints the counts returned to it.

`CodeCommand` — the reverse direction, pseudocode back to code — is a stub: it echoes the argument and writes `TODO: Implement project conversion to pseudocode`. `SymfonyCodeGenerator` exists and is passed to every processor, but nothing calls it yet.

### PseudocodeService

src/Service/PseudocodeService.php is the only service. Its constructor builds the object graph once:

```php
$this->pseudocodeGenerator = new SymfonyPseudocodeGenerator();
$this->pseudocodeGenerator->setParserContext(
    new ParserContext(new ReflectionInheritanceResolver())
);
```

The inheritance resolver works by reflection, so a class must be autoloadable for its parents to be resolved. The processor list is a hard-coded array of class names — `EntityProcessor::class`, `RepositoryProcessor::class` — each constructed with both generators and keyed by class name.

`process(string $pseudocodeDir, string $sourcePath, bool $recursive)` returns `array{scanned: int, generated: string[]}` and splits on what `$sourcePath` is:

- **A file** — `processFile()` skips anything not ending in `.php`, then calls `generateFromFileAndSave()` directly. Processors and `additionalSources` are bypassed entirely on this branch.
- **A directory** — the path becomes the first entry of `$sourcePaths`, then every configured additional source is resolved against the project dir and appended if `is_dir()`. Each path is then run through *every* processor, and the scanned counts and generated file lists are accumulated.

### Processors

src/Processor/AbstractProcessor.php carries all the logic; a concrete processor is a pair of one-line methods. src/Processor/EntityProcessor.php returns `'Entity'` from `getSourceSubDirectory()`, src/Processor/RepositoryProcessor.php returns `'Repository'`. That subdirectory is appended to the source path given by the caller, so `src` is scanned as `src/Entity` and `src/Repository` — the processors define which parts of a Symfony project are covered, and adding a third kind of class means adding a third subclass to the array in `PseudocodeService`.

`process()` builds a `Symfony\Component\Finder\Finder`, filters on `getFilePattern()` (`*.php`, overridable), and applies `$finder->depth('== 0')` unless `$recursive` — recursion is off by default. Note that `Finder::in()` throws `DirectoryNotFoundException` when the directory is absent, so a source path without an `Entity/` subdirectory aborts the run rather than being skipped.

Each matched file goes to the generator with the code directory as the base for the relative output path:

```php
$outputFile = $this->pseudocodeGenerator->generateFromFileAndSave(
    $file,
    $codeDir . '/',
    $pseudocodeRootDir,
);
```

The generator returning a falsy value means no file was written; only truthy returns are collected into `generated`. `getProcessorName()` is abstract and implemented by both subclasses, but nothing under `src/` calls it.

### Generators and the config registry

The two generators are subclasses that add no methods of their own. src/Generator/SymfonyPseudocodeGenerator.php extends `Wexample\Pseudocode\Generator\PseudocodeGenerator` and src/Generator/SymfonyCodeGenerator.php extends `CodeGenerator`; both do nothing but `use WithSymfonyConfigRegistry;`.

That trait, in src/Common/Traits/WithSymfonyConfigRegistry.php, is the whole specialisation mechanism: it reuses the upstream `WithConfigRegistry` and redirects one method.

```php
protected function getConfigRegistryClass(): string
{
    return SymfonyConfigRegistry::class;
}
```

src/Common/SymfonyConfigRegistry.php is an empty extension of `Wexample\Pseudocode\Common\ConfigRegistry`. It is the seam where Symfony-specific parsing or rendering configuration is meant to go; today it inherits everything and overrides nothing, which is why the pseudocode this bundle produces is identical to the upstream package's output.

### The path of a generation run

`php bin/console pseudocode:generate:pseudocode src` →
`PseudocodeCommand::execute()` resolves `src` and the output dir against the project dir →
`PseudocodeService::process()` sees a directory, appends the additional sources from bundles and config →
for each path, `EntityProcessor` then `RepositoryProcessor` run `Finder` over `<path>/Entity` and `<path>/Repository` →
each `SplFileInfo` goes to `SymfonyPseudocodeGenerator::generateFromFileAndSave()`, which parses with a `ReflectionInheritanceResolver` and writes one YAML file under the output dir →
the paths bubble back up as `generated` and the command prints them one per line.

### Tests

phpunit.xml bootstraps `vendor/autoload.php` and declares a single suite over the `tests` directory. That directory is not currently present in the repository.

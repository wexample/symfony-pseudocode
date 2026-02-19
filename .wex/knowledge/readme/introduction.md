`wexample/symfony-pseudocode` generates YAML pseudocode from Symfony PHP classes marked with `#[PseudocodeExport]`.

The package scans source files (by default `src/Entity` and `src/Repository`) and exports structured class metadata (properties, methods, types, defaults) to `.yml` files.

When `inherited: true` is enabled, inheritance export is resolved through PHP reflection only: parent classes, interfaces, and traits are read from autoloadable runtime classes, with no legacy AST fallback.

# Migration Checklist (Option B Strict)

- [ ] Définir le contrat: `inherited=true` => export basé Reflection uniquement (breaking change assumé).
- [ ] Isoler la nouvelle pipeline dans un service dédié (`Reflection*Exporter/Resolver`) sans mélange avec la logique AST existante.
- [ ] Mapper `ReflectionClass`/`ReflectionMethod`/`ReflectionProperty` vers les objets de config YAML (`ClassConfig`, `ClassMethodConfig`, `ClassPropertyConfig`).
- [ ] Gérer strictement l’héritage complet: parents, interfaces, traits, règles de visibilité/override.
- [ ] Supprimer tout fallback legacy pour `inherited=true` (pas de `ClassIndex`/resolver AST dans ce mode).
- [ ] Adapter le point d’entrée Symfony pour router explicitement vers la pipeline reflection quand `inherited=true`.
- [ ] Mettre à jour/étendre l’API d’options si nécessaire pour éviter toute ambiguïté de comportement.
- [ ] Ajouter des tests unitaires et d’intégration ciblés `inherited=true` (cas parent/trait/interface/override).
- [ ] Ajouter des tests d’échec explicites (classe non autoloadable, symbole manquant, conflit invalide).
- [ ] Mettre à jour la documentation package: comportement strict, prérequis autoload, et migration.

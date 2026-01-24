# Keystone CMS

Keystone CMS is a modern, developer-first content management system built in PHP.
It features a clean and minimal core, strict architectural rules, and a robust plugin system based on Composer and dependency injection.

Keystone is designed to be predictable, extensible, and maintainable — even as projects grow.

## Principles
- No magic
- No page builders
- Plugin-first architecture
- Predictable upgrades

## Structure
- Controllers are thin
- Services contain business logic
- Repositories are dumb
- Core is stable, plugins evolve

## Online Updater

Keystone uses versioned, immutable releases.
Activation is via an atomic symlink switch.
Rollback is always possible as long as releases exist.

## Important
bootstrap/app.php contains no domain logic.
All application wiring lives in bootstrap/container.php.

Keystone-rule (This rule is a good one!)

Security helpers are Twig functions, not PHP helpers.
They render HTML but don't contain any business logic.

## Basic directory structure ##

```
src/
├── Application/
│   ├── Settings.php
│   └── Dependencies.php
├── Controller/
│   └── PageController.php
├── Domain/
│   └── Page/
│       ├── Page.php
│       ├── PageRepository.php
│       └── PageService.php
├── Infrastructure/
│   ├── Persistence/
│   │   └── PdoPageRepository.php
│   └── Twig/
│       └── TwigFactory.php
└── routes.php
```

## Plugin Logic ##

``` 
plugins/
└── Pages/
    ├── plugin.php
    |── container.php
    ├── src/
    │   ├── Controller/
    │   │   ├── Admin/PageController.php
    │   │   └── Public/PageController.php
    │   ├── Domain/
    │   │   ├── Page.php
    │   │   ├── PageService.php
    │   │   ├── PagePolicy.php
    │   │   └── PageRepositoryInterface.php
    │   └── Infrastructure/
    │       └── Persistence/PageRepository.php
    ├── routes/
    │   ├── admin.php
    │   └── public.php
    └── views/
        ├── admin/pages/
        └── public/page.twig

```

## afspraken ##

Controller  →  Service  →  Repository
(UI)           (use-cases)   (data)

## 10. Keystone “golden rules” (nu compleet) ##

1. Autowiring everywhere
2. Controllers praten alleen met Services
3. Services bevatten alle use-cases
4. Repositories zijn dom
5. Plugins zijn self-contained
6. Core kent geen plugins
7. Twig base layout + blocks
8. Homepage = expliciete business rule
9. Users zijn gebonden aan rollen
10. De rollen zijn verbonden aan policies

Dit is een extreem solide basis — hier ga je jaren plezier van hebben.

met:
namespace-regels
namespace begint met Keystone
namespace naam plugins is Keystone\Plugins
graag namespaces en use statements bijvoegen
plugin lifecycle
DI afspraken
CSRF standaard {{ csrf() }}
Twig namespaces
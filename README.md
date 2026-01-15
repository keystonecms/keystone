# Keystone CMS

A stable, developer-first CMS core.

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

## Important
bootstrap/app.php contains no domain logic.
All application wiring lives in bootstrap/container.php.

Keystone-regel (dit is een goede!)

Security helpers zijn Twig functions, geen PHP helpers.
Ze renderen HTML, maar bezitten geen business logic.

## Basis directory structure ##

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

Dit is een extreem solide basis — hier ga je jaren plezier van hebben.

met:
namespace-regels
plugin lifecycle
DI afspraken
CSRF standaard
Twig namespaces
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


## Plugin Logic ##

plugins/
└── Pages/
    ├── plugin.php
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

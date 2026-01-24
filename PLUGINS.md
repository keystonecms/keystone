# Keystone CMS – Plugin Development Guide

This document describes **how to build plugins for Keystone CMS**, including technical requirements, architecture rules, lifecycle hooks, and available integrations.

Keystone plugins are **first‑class Composer packages**, not folders or hacks.

---

## 1. What Is a Keystone Plugin?

A Keystone plugin is:

* A **Composer package**
* With its **own Git repository**
* Autoloaded via **PSR‑4**
* Discovered automatically by Keystone
* Enabled / disabled via the admin UI
* Loaded at runtime via the `PluginLoader`

> **Core rule:**
> *The CMS core does not know any plugins.*

---

## 2. Mandatory Plugin Requirements

Every plugin **must** meet all requirements below.

### 2.1 Must Be a Composer Package

Each plugin **must have its own `composer.json`**.

```json
{
  "name": "keystone/plugin-pages",
  "type": "keystone-plugin",
  "autoload": {
    "psr-4": {
      "Keystone\\Plugin\\Pages\\": "src/"
    }
  },
  "extra": {
    "keystone": {
      "plugin-class": "Keystone\\Plugin\\Pages\\Plugin"
    }
  }
}
```

**Required fields:**

| Field                         | Purpose                   |
| ----------------------------- | ------------------------- |
| `name`                        | Composer package name     |
| `type`                        | Must be `keystone-plugin` |
| `autoload.psr-4`              | Plugin namespace mapping  |
| `extra.keystone.plugin-class` | Entry class               |

---

### 2.2 Must Be a Git Repository

Plugins **must** be version‑controlled independently.

```bash
git init
git add .
git commit -m "Initial plugin"
```

This is required for:

* Composer dependency resolution
* Versioning
* Updates
* Marketplace compatibility

---

## 3. Directory Structure

Recommended structure:

```
plugin-name/
├── composer.json
├── src/
│   ├── Plugin.php
│   ├── Controller/
│   ├── Domain/
│   ├── Infrastructure/
│   └── Policy/
├── routes/
│   ├── admin.php
│   └── public.php
├── migrations/
│   └── Version20260101.php
├── views/
│   ├── admin/
│   └── public/
└── README.md
```

---

## 4. Namespaces (VERY IMPORTANT)

All plugin namespaces **must follow this exact format**:

```
Keystone\Plugin\<PluginName>\
```

### ✅ Correct

```php
namespace Keystone\Plugin\Pages\Domain;
```

### ❌ Incorrect

```php
namespace Keystone\Plugins\Pages\Domain; // ❌
```

> **Rule:** Always `Plugin` (singular), never `Plugins`.

Namespace mismatches will cause fatal errors.

---

## 5. Plugin Entry Class

Every plugin must provide **one named entry class**.

### 5.1 PluginInterface

```php
namespace Keystone\Core\Plugin;

use Psr\Container\ContainerInterface;
use Slim\App;

interface PluginInterface
{
    public function getName(): string;
    public function getVersion(): string;
    public function getDescription(): string;

    public function register(ContainerInterface $container): void;
    public function boot(App $app, ContainerInterface $container): void;
}
```

---

### 5.2 Example Plugin Class

```php
namespace Keystone\Plugin\Pages;

use Keystone\Core\Plugin\PluginInterface;
use Psr\Container\ContainerInterface;
use Slim\App;

final class Plugin implements PluginInterface
{
    public function getName(): string
    {
        return 'pages';
    }

    public function getVersion(): string
    {
        return 'dev';
    }

    public function getDescription(): string
    {
        return 'Pages plugin';
    }

    public function register(ContainerInterface $container): void
    {
        // register services, repositories, policies
    }

    public function boot(App $app, ContainerInterface $container): void
    {
        // register routes, middleware, twig namespaces
    }
}
```

### ❌ Anonymous classes are NOT allowed

```php
return new class implements PluginInterface { ... }; // ❌
```

Plugins must be **discoverable by class name**.

---

## 6. Dependency Injection (DI)

Plugins may register services using the Keystone container.

```php
$container->set(PageService::class, autowire());
```

**Rules:**

* Controllers talk **only** to Services
* Services contain **all use‑cases**
* Repositories are **data‑only**
* Core services may be injected, but **never modified**

---

## 7. Routing

Plugins may define routes in separate files:

```
routes/admin.php
routes/public.php
```

Loaded during `boot()`.

```php
$app->get('/admin/pages', PageController::class . ':index');
```

---

## 8. Twig Integration

Plugins may register Twig namespaces:

```php
$twig = $container->get(Twig::class);
$twig->getLoader()->addPath(
    __DIR__ . '/../views',
    'pages'
);
```

Usage:

```twig
{% include '@pages/admin/index.twig' %}
```

---

## 9. Database Migrations

Plugins may provide migrations.

```
migrations/
├── Version20260101.php
```

Migrations are executed when a plugin is enabled.

---

## 10. Enable / Disable Lifecycle

| State      | Meaning           |
| ---------- | ----------------- |
| Discovered | Plugin exists     |
| Installed  | Known in database |
| Enabled    | Loaded at runtime |
| Disabled   | Ignored           |

Disabled plugins have **no runtime effect**.

---

## 11. Plugin Load Order

Recommended strategy:

| Type                      | Order   |
| ------------------------- | ------- |
| Core                      | 0–99    |
| Feature plugins           | 100–899 |
| Catch‑all plugins (Pages) | 999     |

---

## 12. What Plugins MAY Do

✅ Register services
✅ Add routes
✅ Add Twig views
✅ Add policies
✅ Add migrations
✅ Extend menus
✅ Use core services

---

## 13. What Plugins MUST NOT Do

❌ Modify core services
❌ Assume load order unless specified
❌ Access globals
❌ Use anonymous plugin classes
❌ Bypass DI container
❌ Register routes outside `boot()`

---

## 14. Golden Rules Summary

1. Plugins are Composer packages
2. Plugins live outside the CMS core
3. Core never knows plugins
4. One named Plugin class
5. Correct PSR‑4 namespaces
6. Controllers → Services → Repositories
7. Loader loads **only enabled plugins**
8. No anonymous classes
9. No global helpers
10. Clean separation of concerns

---

## 15. Final Note

If you follow this document **exactly**, your plugin will:

* Be discoverable
* Be enable/disable‑safe
* Be upgrade‑ready
* Work in production
* Integrate cleanly with Keystone CMS

This is **framework‑grade extensibility**, not CMS magic.

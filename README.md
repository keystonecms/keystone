# Keystone CMS

**Keystone CMS** is a modern, developer‑first content management system built in PHP.
It focuses on **clean architecture**, a **small and predictable core**, and a **powerful plugin system** that scales from simple websites to complex platforms.

Keystone is not a monolith with optional plugins — it is a **core framework where everything is a plugin**.

---

## ✨ Key Features

* 🧱 **Clean, minimal core** — no hidden magic
* 🔌 **First‑class plugin system** (Composer‑based)
* 🧠 **Strict architecture rules** (Controllers → Services → Repositories)
* 🔐 **Role & policy driven authorization**
* 🧩 **Explicit extension points** (menus, dashboard widgets, routes)
* 🧪 **Testable by design**
* 🚀 **Production‑ready foundation**

---

## 🎯 Philosophy

Keystone CMS is designed for developers who:

* value **clarity over convenience**
* want **predictable behavior** in production
* prefer **explicit contracts** over hidden coupling
* build projects that must be **maintainable for years**

> **Golden rule:**
> The core never knows about plugins.

Everything beyond the core is implemented as a plugin.

---

## 🧱 Architecture Overview

Keystone enforces a strict separation of concerns:

```
Controller  →  Service  →  Repository
(UI)           (use‑cases)   (data access)
```

### Core principles

* Controllers contain **no business logic**
* Services define **all use‑cases**
* Repositories are **data‑only**
* Plugins are **self‑contained**
* No global state, no magic discovery

---

## 🔌 Plugin System

Plugins are **Composer packages**, not folders copied into the core.

Each plugin:

* lives in its own Git repository
* has its own `composer.json`
* exposes a single `Plugin` class
* can be enabled or disabled at runtime

Example:

```bash
composer require keystone/plugin-pages
```

### What plugins can do

* register services and repositories
* add admin and public routes
* add dashboard widgets
* extend menus
* add Twig views
* provide database migrations

### What plugins cannot do

* modify core services
* access globals
* rely on load order implicitly
* bypass the DI container

---

## 📊 Dashboard & Widgets

The admin dashboard is fully extensible.

* Core provides the dashboard framework
* Plugins may register **dashboard widgets**
* Widgets only exist when the plugin is enabled

This guarantees:

* no broken dashboards
* no feature‑coupling in core
* clean extensibility

---

## 🧭 Menus & Navigation

Navigation is handled through an explicit registry.

* Core defines menu extension points
* Plugins register menu items when enabled
* Authorization is handled via policies

No plugin assumptions. No hard‑coded links.

---

## 🔐 Authorization & Policies

Keystone uses **policy‑based authorization**:

* users have roles
* roles map to policies
* policies guard actions

This keeps permissions:

* explicit
* testable
* consistent across plugins

---

## 🧪 Testing

Keystone is designed to be testable at every layer:

* services are framework‑agnostic
* repositories can be mocked
* plugins can be tested in isolation

---

## 📦 Example Plugins

* [`plugin-hello-world`](https://github.com/keystonecms/plugin-hello-world)
* `plugin-pages`

The **Hello World plugin** is the canonical reference implementation for plugin developers.

---

## 🧰 Requirements

* PHP 8.3 or higher
* Composer

---

## 🚧 Project Status

Keystone CMS is under active development.

* Core architecture is stable
* Plugin system is production‑ready
* Public API is evolving carefully

Expect rapid iteration with a strong focus on backwards compatibility.

---

## 🤝 Contributing

Contributions are welcome.

If you want to:

* report a bug
* propose a feature
* build a plugin

please open an issue or discussion first.

---

## 📜 License

Keystone CMS is currently released under a proprietary license.
Licensing may evolve as the project matures.

---

## 🌍 Keystone CMS Ecosystem

* Core: [https://github.com/keystonecms/keystone](https://github.com/keystonecms/keystone)
* Plugins: [https://github.com/keystonecms](https://github.com/keystonecms)
* Website: [https://keystone-cms.com](https://keystone-cms.com)
---

**Keystone CMS** — a CMS for developers who care about structure, clarity, and longevity.

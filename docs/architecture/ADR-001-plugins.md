# Keystone Plugin Architecture – Stabiliteitsrichtlijnen

Dit document beschrijft **hoe we voorkomen dat we in de toekomst “van links naar rechts gaan”** bij fundamentele keuzes rondom plugins, discovery, DI en installatie.

Doel:
- Rust in de architectuur
- Voorspelbaar gedrag
- Geen half-afgebouwde paden
- Bewuste evolutie i.p.v. trial-and-error

---

## 1. Architectuur-beslissingen zijn expliciet (ADR)

Elke fundamentele keuze wordt vastgelegd als een **Architecture Decision Record (ADR)**.

### Voorbeeld

**ADR-001 – Plugin distributie**

> Keystone plugins zijn **Composer-based packages**.  
> Filesystem plugins worden **niet** ondersteund in v1.

Gevolgen:
- Geen ZIP installer
- Geen filesystem discovery
- Geen custom autoloaders

Een ADR:
- is kort (1–2 alinea’s)
- beschrijft **wat gekozen is**
- beschrijft **wat expliciet is uitgesloten**

📌 **Geen code zonder ADR bij fundamentele wijzigingen.**

---

## 2. Single Source of Truth per as

Voor elk technisch domein is er **exact één waarheid**.

| Vraag | Antwoord |
|-----|---------|
| Waar komen plugins vandaan? | Composer |
| Wie installeert plugins? | Composer |
| Wie regelt autoloading? | Composer |
| Waar komt plugin metadata vandaan? | composer.json (`extra.keystone`) |
| Wie bepaalt enabled/disabled? | Database (`plugins` table) |
| Wie laadt plugins runtime? | `PluginLoader` |
| Wie ontdekt plugins? | `PluginDiscovery` (composer.lock) |

❗ Als een vraag **meer dan één antwoord** krijgt → STOP.

---

## 3. Verboden-lijst (net zo belangrijk als features)

Wat **expliciet niet mag** in Keystone v1:

```text
❌ spl_autoload_register in plugins
❌ Handmatige require_once van plugin classes
❌ ZIP-based plugin install
❌ Filesystem plugin discovery
❌ autowire() op niet-autoloadbare classes
❌ Meerdere discovery-strategieën tegelijk

## [1.2.4](https://github.com/keystonecms/keystone/compare/v1.2.3...v1.2.4) (2026-01-26)


### Bug Fixes

* added some disclaimers in the code ([9be609e](https://github.com/keystonecms/keystone/commit/9be609e07c0058dad0601b783040360049e4bd29))

## [1.2.3](https://github.com/keystonecms/keystone/compare/v1.2.2...v1.2.3) (2026-01-26)


### Bug Fixes

* changed the ENV in signing.yml ([ce4981d](https://github.com/keystonecms/keystone/commit/ce4981d55f29638aa607e913a2948e31ba649cce))

## [1.2.2](https://github.com/keystonecms/keystone/compare/v1.2.1...v1.2.2) (2026-01-26)


### Bug Fixes

* correct update detection system ([0131aaa](https://github.com/keystonecms/keystone/commit/0131aaa6f0e06000dada770d64a70b61bee40557))

## [1.2.1](https://github.com/keystonecms/keystone/compare/v1.2.0...v1.2.1) (2026-01-26)


### Bug Fixes

* clean up unnessecary code ([a64abf3](https://github.com/keystonecms/keystone/commit/a64abf3fd46421a41779876a55a829acd7a15866))

# [1.2.0](https://github.com/keystonecms/keystone/compare/v1.1.0...v1.2.0) (2026-01-26)


### Features

* auto signing of ZIP files ([549160b](https://github.com/keystonecms/keystone/commit/549160bbcaa6a8f40873256688ec492297a196c5))

# [1.1.0](https://github.com/keystonecms/keystone/compare/v1.0.1...v1.1.0) (2026-01-25)


### Features

* plugin install via dashboard by composer integrated, remove still needs to be implemented ([be5ad5f](https://github.com/keystonecms/keystone/commit/be5ad5f20c9a0c05d93ff97051ac8eb19531097c))

## [1.0.1](https://github.com/keystonecms/keystone/compare/v1.0.0...v1.0.1) (2026-01-25)


### Bug Fixes

* fixed composer.json and switched to composer install ([f39a830](https://github.com/keystonecms/keystone/commit/f39a8304200a310ea71827dd268c89dc37566b02))

# 1.0.0 (2026-01-25)


### Bug Fixes

* added errorid to error log ([01bea41](https://github.com/keystonecms/keystone/commit/01bea41b96c13e0e075cd138965b1926a7b7de27))
* changed test suite use own app ([d93af12](https://github.com/keystonecms/keystone/commit/d93af1202be21c4763e516853e91a936ca276fc0))
* changed the tests for middleware ([daf4723](https://github.com/keystonecms/keystone/commit/daf47237eb3d01b495617071b8f1240db1e95927))
* composer.json error in github ([ee656bf](https://github.com/keystonecms/keystone/commit/ee656bfb70817ba2dd423c23b2180eb3677af05a))
* composer.json file deleted comma ([85ec8bb](https://github.com/keystonecms/keystone/commit/85ec8bb2537bd6835aa6eacd62d50b7f1d4e51fe))
* fixed and added some tests ([cb4136c](https://github.com/keystonecms/keystone/commit/cb4136cd19ee1b2afcde57b6d1d3741633e7b1ba))
* run composer update for lock file ([89ff612](https://github.com/keystonecms/keystone/commit/89ff6126b9f594ae46f34e65e6d8cbd804115abc))


### Features

* added releaserc.json ([93d0592](https://github.com/keystonecms/keystone/commit/93d059202ce4da6eca845fd7720b68f2e7c9d7b7))
* changed the versions file ([c4f9e5d](https://github.com/keystonecms/keystone/commit/c4f9e5df0e77c808007dae8889d9651c6edfc4b7))
* start with plugin marketplace and fixed some core troubles ([96785a5](https://github.com/keystonecms/keystone/commit/96785a5a14fe06c8073db33e896c278aa913fca9))

## Plugin installer (2026-01-24)
- Created a composer plugin installer
- Enable/disable plugins via admin GUI
- Created Keystone Organization
- Registered domain https://keystone-cms.com
- Moved admin twig templates from pages plugin to core

## Added a blog plugin (2026-01-23)
- Added blog plugin
- Continued with multi language support (domain)
- Dashboard menu support in plugins

## Added a shopping cart (2026-01-22)
- Added shopping cart
- 3 payments providers (Stripe, Mollie, PayPal)
- Started multi language support

## Added a CI/CD pipeline (2026-01-18)
- Github pipeline
- Added online installer
- Added online updater

## Added a Auth plugin (2026-01-17)
- Sign-up
- Supply password
- Email tokens
- Role based

## Added internal links for pages (2026-01-16)
- Internal links option for pages
- Internal links plugin
- SEO plugin

## More functionality on pages plugin (2026-01-15)

- WYSIWYG editor
- Auto save
- Publish/unpublish
- Draft
- Paste images in page
- Template selection

## Started with Media plugin (2026-01-14)

Started with another plugin functionality (media).

## Started with Pages plugin (2026-01-13)

Started with the first plugin functionality (pages).

## Started with new CMS Keystone-cms (2026-01-12)

Started with the core functionality of the Keystone CMS.

New idea CMS with plugin functionality based on Slim 4, Twig template system, Dependecy injection and autowiring. Decided on name and colors. Keystone CMS project initiated.

# Changelog

All notable changes to `socialment` will be documented in this file.

> [!NOTE]
Due to an issue in the workflow that generates this changelog, the first two entries were manually added.

## v4.0.0-beta.1 - 2025-08-20

### What's Changed

* Laravel 12 + FilamentPHP 4 support!
* build(deps): bump dependabot/fetch-metadata from 2.3.0 to 2.4.0 by @dependabot[bot] in https://github.com/chrisreedio/socialment/pull/75
* build(deps): bump aglipanci/laravel-pint-action from 2.5 to 2.6 by @dependabot[bot] in https://github.com/chrisreedio/socialment/pull/77
* chore(deps): update PHP and package versions in composer.json by @chrisreedio in https://github.com/chrisreedio/socialment/pull/80

**Full Changelog**: https://github.com/chrisreedio/socialment/compare/v3.10.1...v4.0.0-beta.1

## v3.10.1 - 2025-04-21

### What's Changed

* build(deps): bump dependabot/fetch-metadata from 2.1.0 to 2.2.0 by @dependabot in https://github.com/chrisreedio/socialment/pull/64
* build(deps): bump dependabot/fetch-metadata from 2.2.0 to 2.3.0 by @dependabot in https://github.com/chrisreedio/socialment/pull/71
* build(deps): bump aglipanci/laravel-pint-action from 2.4 to 2.5 by @dependabot in https://github.com/chrisreedio/socialment/pull/72
* remove illuminate/contracts to support any laravel version by @atmonshi in https://github.com/chrisreedio/socialment/pull/74

**Full Changelog**: https://github.com/chrisreedio/socialment/compare/v3.9.1...v3.10.1

## v3.9.1 - 2024-07-02

### What's Changed

* 🐛 fix: initialized $createUserClosure to null in `SocialmentPlugin.php` by @chrisreedio in https://github.com/chrisreedio/socialment/pull/63

**Full Changelog**: https://github.com/chrisreedio/socialment/compare/v3.9.0...v3.9.1

Thanks to @Cybrarist for finding the bug and suggesting a fix in #62

## v3.9.0 - 2024-06-18

### What's Changed

* ✨ feat: added custom user creation logic in `SocialmentPlugin` by @chrisreedio in https://github.com/chrisreedio/socialment/pull/59
* 🛠️ fix(controllers): added check for null user in `SocialmentController` by @chrisreedio in https://github.com/chrisreedio/socialment/pull/60

**Full Changelog**: https://github.com/chrisreedio/socialment/compare/v3.8.0...v3.9.0

## v3.8.0 - 2024-05-04

### What's Changed

* ✨ feat: Added support for custom scopes in social providers by @chrisreedio in https://github.com/chrisreedio/socialment/pull/58

**Full Changelog**: https://github.com/chrisreedio/socialment/compare/v3.7.0...v3.8.0

## v3.7.0 - 2024-05-03

### What's Changed

* Bump dependabot/fetch-metadata from 1.6.0 to 2.0.0 by @dependabot in https://github.com/chrisreedio/socialment/pull/52
* hot fix: add missing import by @atmonshi in https://github.com/chrisreedio/socialment/pull/51
* Bump aglipanci/laravel-pint-action from 2.3.1 to 2.4 by @dependabot in https://github.com/chrisreedio/socialment/pull/54
* Bump dependabot/fetch-metadata from 2.0.0 to 2.1.0 by @dependabot in https://github.com/chrisreedio/socialment/pull/56
* add `getProviders` by @atmonshi in https://github.com/chrisreedio/socialment/pull/55
* (fix): typehint by @pepperfm in https://github.com/chrisreedio/socialment/pull/42

### New Contributors

* @pepperfm made their first contribution in https://github.com/chrisreedio/socialment/pull/42

**Full Changelog**: https://github.com/chrisreedio/socialment/compare/v3.6.1...v3.7.0

## v3.6.1 - 2024-03-20

### What's Changed

* 🔧 fix(icons): Exchanged FA Pro icon for a free version by @chrisreedio in https://github.com/chrisreedio/socialment/pull/49

**Full Changelog**: https://github.com/chrisreedio/socialment/compare/v3.6.0...v3.6.1

## v3.6.0 - 2024-03-15

### What's Changed

* Allow for custom 'me' SPA response by @chrisreedio in https://github.com/chrisreedio/socialment/pull/47
* Narrowed test matrix for CI runs by @chrisreedio in https://github.com/chrisreedio/socialment/pull/46

**Full Changelog**: https://github.com/chrisreedio/socialment/compare/v3.5.0...v3.6.0

## v3.5.0 - 2024-03-12

### SPA Support

This feature is still experimental and a work in progress. Use at your own risk.

### Configuration Deprecations

Support for configuring providers via the configuration file has been deprecated in favor of the configuring the providers via the panel provider.

Configuring options at the panel level allows for far better multi-panel support among many other improvements.

### Laravel 11 Support

This package should now work with Laravel 11 projects! 🚀

### What's Changed

* Save Previous Panel URL for after Login by @chrisreedio in https://github.com/chrisreedio/socialment/pull/34
* Bump aglipanci/laravel-pint-action from 2.3.0 to 2.3.1 by @dependabot in https://github.com/chrisreedio/socialment/pull/39
* Bump ramsey/composer-install from 2 to 3 by @dependabot in https://github.com/chrisreedio/socialment/pull/41
* SPA Documentation Improvements by @chrisreedio in https://github.com/chrisreedio/socialment/pull/44
* Laravel 11 Support by @chrisreedio in https://github.com/chrisreedio/socialment/pull/45

**Full Changelog**: https://github.com/chrisreedio/socialment/compare/v3.4.1...v3.5.0

## v3.4.1 - 2023-11-16

### What's Changed

- Fixes Test Workflow - Updated Minimum Dependency Versions by @chrisreedio in https://github.com/chrisreedio/socialment/pull/32
- hot fix for github provider for nullable names by @atmonshi in https://github.com/chrisreedio/socialment/pull/35

### New Contributors

- @atmonshi made their first contribution in https://github.com/chrisreedio/socialment/pull/35

**Full Changelog**: https://github.com/chrisreedio/socialment/compare/v3.4.0...v3.4.1

## v3.4.0 - 2023-11-02

### What's Changed

- Per-Panel Configuration of Providers by @chrisreedio in https://github.com/chrisreedio/socialment/pull/31

This feature is still highly experimental and the signature is highly likely to change (become more standardized).

**Full Changelog**: https://github.com/chrisreedio/socialment/compare/v3.3.0...v3.4.0

## v3.3.0 - 2023-11-02

### What's Changed

- Multiple Pre/Post Login Hooks/Callbacks by @chrisreedio in https://github.com/chrisreedio/socialment/pull/29
- Update README to include pre-login hooks by @chrisreedio in https://github.com/chrisreedio/socialment/pull/30

**Full Changelog**: https://github.com/chrisreedio/socialment/compare/v3.2.1...v3.3.0

## v3.2.1 - 2023-11-02

### What's Changed

- Fixed assigning the `preLogin` hook to the `postLoginCallback`.
- Migration to allow nullable passwords for users by @chrisreedio in https://github.com/chrisreedio/socialment/pull/28

**Full Changelog**: https://github.com/chrisreedio/socialment/compare/v3.2.0...v3.2.1

## v3.2.0 - 2023-10-31

**Socialment is still considered beta but should no longer require the dev stability composer setting**

### What's Changed

- Redirects InvalidStateExceptions to Login Route by @chrisreedio in https://github.com/chrisreedio/socialment/pull/22
- Prelogin Hook and Aborted Login Exceptions by @chrisreedio in https://github.com/chrisreedio/socialment/pull/23

**Full Changelog**: https://github.com/chrisreedio/socialment/compare/3.1.1-beta...v3.2.0

## 3.1.1-beta - 2023-10-25

### What's Changed

- Update Connected Account Details on Login by @chrisreedio in https://github.com/chrisreedio/socialment/pull/21

**Full Changelog**: https://github.com/chrisreedio/socialment/compare/v3.1.0-beta...3.1.1-beta

## v3.1.0-beta - Post Login Callback - 2023-10-16

### What's Changed

- Fixing changelog generation by @chrisreedio in https://github.com/chrisreedio/socialment/pull/15
- Bump stefanzweifel/git-auto-commit-action from 4 to 5 by @dependabot in https://github.com/chrisreedio/socialment/pull/16
- Login Callback by @chrisreedio in https://github.com/chrisreedio/socialment/pull/20

**Full Changelog**: https://github.com/chrisreedio/socialment/compare/v3.0.1-beta...v3.1.0-beta

## 3.0.1-beta - 2023-09-26

### What's Changed

- **Handling existing user accounts** by @chrisreedio in https://github.com/chrisreedio/socialment/pull/14
- Fix README badge by @chrisreedio in https://github.com/chrisreedio/socialment/pull/9
- Bump actions/checkout from 3 to 4 by @dependabot in https://github.com/chrisreedio/socialment/pull/10
- Added sample provider configuration to README by @chrisreedio in https://github.com/chrisreedio/socialment/pull/12
- Formatting by @chrisreedio in https://github.com/chrisreedio/socialment/pull/13

### New Contributors

- @dependabot made their first contribution in https://github.com/chrisreedio/socialment/pull/10

**Full Changelog**: https://github.com/chrisreedio/socialment/compare/v3.0.0-beta...v3.0.1-beta

## 3.0.0-beta - 2023-09-05

> [!WARNING]
This package is still in ***BETA***.
It has only been tested at length with the Azure AD provider.
Please report issues if you find them and I'm open to PRs!

### What's Changed

- Package is working in Demo Project by @chrisreedio in https://github.com/chrisreedio/socialment/pull/1
- Update README.md by @chrisreedio in https://github.com/chrisreedio/socialment/pull/2
- Adding package description. by @chrisreedio in https://github.com/chrisreedio/socialment/pull/3
- README updates and config formatting. by @chrisreedio in https://github.com/chrisreedio/socialment/pull/4
- README updates by @chrisreedio in https://github.com/chrisreedio/socialment/pull/5
- PHPStan passing by @chrisreedio in https://github.com/chrisreedio/socialment/pull/6
- Added gap when multiple providers in use. by @chrisreedio in https://github.com/chrisreedio/socialment/pull/7
- Added warning to readme about the current beta state. by @chrisreedio in https://github.com/chrisreedio/socialment/pull/8

### New Contributors

- @chrisreedio made their first contribution in https://github.com/chrisreedio/socialment/pull/1

**Full Changelog**: https://github.com/chrisreedio/socialment/commits/v3.0.0-beta

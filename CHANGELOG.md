# Changelog

All notable changes to `socialment` will be documented in this file.

> [!NOTE]
Due to an issue in the workflow that generates this changelog, the first two entries were manually added.

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

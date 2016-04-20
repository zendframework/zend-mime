# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.5.2 - 2016-04-20

### Added

- [#8](https://github.com/zendframework/zend-mime/pull/8) and
  [#11](https://github.com/zendframework/zend-mime/pull/11) port documentation
  from the zf-documentation repo, and publish it to
  https://zendframework.github.io/zend-mime/

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#2](https://github.com/zendframework/zend-mime/pull/2) fixes
  `Mime::encodeBase64()`'s behavior when presented with lines of invalid
  lengths (not multiples of 4).
- [#4](https://github.com/zendframework/zend-mime/pull/4) modifies
  `Mime::encodeQuotedPrintable()` to ensure it never creates a header line
  consisting of only a dot (concatenation character), a situation that can break
  parsing by Outlook.
- [#7](https://github.com/zendframework/zend-mime/pull/7) provides a patch that
  allows parsing MIME parts that have no headers.
- [#9](https://github.com/zendframework/zend-mime/pull/9) updates the
  dependencies to:
  - allow PHP 5.5+ or PHP 7+ versions.
  - allow zend-stdlib 2.7+ or 3.0+ verions.

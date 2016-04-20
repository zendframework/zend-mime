# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.5.2 - TBD

### Added

- Nothing.

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

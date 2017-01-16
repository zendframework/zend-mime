# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.6.1 - 2017-01-16

### Added

- [#22](https://github.com/zendframework/zend-mime/pull/22) adds the ability to
  decode a single-part MIME message via `Zend\Mime\Message::createFromMessage()`
  by omitting the `$boundary` argument.

### Changes

- [#14](https://github.com/zendframework/zend-mime/pull/14) adds checks for
  duplicate parts when adding them to a MIME message, and now throws an
  `InvalidArgumentException` when detected.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#13](https://github.com/zendframework/zend-mime/pull/13) fixes issues with
  qp-octets produced by Outlook.
- [#17](https://github.com/zendframework/zend-mime/pull/17) fixes a syntax error
  in how are thrown by `Zend\Mime\Part::setContent()`.
- [#18](https://github.com/zendframework/zend-mime/pull/18) fixes how non-ASCII
  header values are encoded, ensuring that it allows the first word to be of
  arbitrary length.

## 2.6.0 - 2016-04-20

### Added

- [#6](https://github.com/zendframework/zend-mime/pull/6) adds
  `Mime::mimeDetectCharset()`, which can be used to detect the charset
  of a given string (usually a header) according to the rules specified in
  RFC-2047.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

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

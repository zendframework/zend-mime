# Introduction

`Zend\Mime\Mime` is a support class for handling multipart
[MIME](https://en.wikipedia.org/wiki/MIME) messages;
[zend-mail](https://github.com/zendframework/zend-mail) relies on it for both
parsing and creating multipart messages. [`Zend\Mime\Message`](message.md) can
also be consumed by applications requiring general MIME support.

## Static Methods and Constants

`Zend\Mime\Mime` provides a set of static helper methods to work with MIME:

- `Zend\Mime\Mime::isPrintable()`: Returns `TRUE` if the given string contains
  no unprintable characters, `FALSE` otherwise.
- `Zend\Mime\Mime::encode()`: Encodes a string with the specified encoding.
- `Zend\Mime\Mime::encodeBase64()`: Encodes a string into base64 encoding.
- `Zend\Mime\Mime::encodeQuotedPrintable()`: Encodes a string with the
  quoted-printable mechanism.
- `Zend\Mime\Mime::encodeBase64Header()`: Encodes a string into base64 encoding
  for Mail Headers.
- `Zend\Mime\Mime::encodeQuotedPrintableHeader()`: Encodes a string with the
  quoted-printable mechanism for Mail Headers.
- `Zend\Mime\Mime::mimeDetectCharset()`: detects if a string is encoded as
  ASCII, Base64, or quoted-printable.

`Zend\Mime\Mime` defines a set of constants commonly used with MIME messages:

* `Zend\Mime\Mime::TYPE_OCTETSTREAM`: 'application/octet-stream'
* `Zend\Mime\Mime::TYPE_TEXT`: 'text/plain'
* `Zend\Mime\Mime::TYPE_HTML`: 'text/html'
* `Zend\Mime\Mime::ENCODING_7BIT`: '7bit'
* `Zend\Mime\Mime::ENCODING_8BIT`: '8bit'
* `Zend\Mime\Mime::ENCODING_QUOTEDPRINTABLE`: 'quoted-printable'
* `Zend\Mime\Mime::ENCODING_BASE64`: 'base64'
* `Zend\Mime\Mime::DISPOSITION_ATTACHMENT`: 'attachment'
* `Zend\Mime\Mime::DISPOSITION_INLINE`: 'inline'
* `Zend\Mime\Mime::MULTIPART_ALTERNATIVE`: 'multipart/alternative'
* `Zend\Mime\Mime::MULTIPART_MIXED`: 'multipart/mixed'
* `Zend\Mime\Mime::MULTIPART_RELATED`: 'multipart/related'

## Instantiating Zend\\Mime

When instantiating a `Zend\Mime\Mime` object, a MIME boundary is stored that is
used for all instance calls. If the constructor is called with a string
parameter, this value is used as the MIME boundary; if not, a random MIME
boundary is generated.

A `Zend\Mime\Mime` object has the following methods:

- `boundary()`: Returns the MIME boundary string.
- `boundaryLine()`: Returns the complete MIME boundary line.
- `mimeEnd()`: Returns the complete MIME end boundary line.

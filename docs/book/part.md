# Zend\\Mime\\Part

`Zend\Mime\Part` represents a single part of a MIME message. It contains the
actual content of the message part, plus information about its encoding,
content type, and original filename. Finally, it provides a method for
generating a string from the stored data.

`Zend\Mime\Part` objects can be added to [Zend\Mime\Message](message.md)
instances to assemble a complete multipart message.

## Instantiation

`Zend\Mime\Part` is instantiated with a string representing the message part's
content. The type is assumed to be OCTET-STREAM, with an 8-bit encoding. After
instantiating a `Zend\Mime\Part`, meta information can be set directly on its
attributes:

```php
public $type = Zend\Mime\Mime::TYPE_OCTETSTREAM;
public $encoding = Zend\Mime\Mime::ENCODING_8BIT;
public $id;
public $disposition;
public $filename;
public $description;
public $charset;
public $boundary;
public $location;
public $language;
```

## Methods for rendering the message part to a string

`getContent()` returns the encoded content of the `Zend\Mime\Part` as a string
using the encoding specified in the attribute `$encoding`. Valid values are
`Zend\Mime\Mime::ENCODING_*`. Character set conversions are not performed.

`getHeaders()` returns the MIME headers for the `Part` as generated from the
information in the publicly accessible attributes. The attributes of the object
need to be set correctly before this method is called.

- `$charset` has to be set to the actual charset of the content if it is a text
  type (text or HTML).
- `$id` may be set to identify a Content-ID for inline images in an HTML mail.
- `$filename` specifies the name of the file at the time of creation.
- `$disposition` defines if the file should be treated as an attachment or if
  it is used inside the (HTML) mail (inline).
- `$description` is only used for informational purposes.
- `$boundary` defines the string to use as a part boundary.
- `$location` can be used as resource URI that has relation to the content.
- `$language` defines the content language.

## Available methods

A `Zend\Mime\Part` object has the following methods:

- `isStream`: Check if this `Part` can be read as a stream. You can specify a
  PHP stream resource when creating the content in order to reduce CPU and/or
  memory overhead; if you do, this value will be toggled to `true`.
- `getEncodedStream`: If the `Part` was created with a stream, return a
  filtered stream for reading the content. Useful for large file attachments.
- `getContent($EOL = Zend\Mime\Mime::LINEEND)`: Get the content of the current
  `Zend\Mime\Part` in the given encoding.
- `getRawContent`: Get the raw, unencoded content for the current `Part`.
- `getHeadersArray($EOL = Zend\Mime\Mime::LINEEND)`: Create and return the
  array of headers for the current `Part`.
- `getHeaders($EOL = Zend\Mime\Mime::LINEEND)`: Return the headers for the
  current `Part` as a string.

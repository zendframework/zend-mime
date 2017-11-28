# Zend\\Mime\\Message

`Zend\Mime\Message` represents a MIME compliant message that can contain one or
more separate Parts (represented as [Zend\Mime\Part](part.md) instances).
Encoding and boundary handling are handled transparently by the class.
`Message` instances can also be generated from MIME strings.

## Instantiation

There is no explicit constructor for `Zend\Mime\Message`.

## Adding MIME Parts

[Zend\Mime\Part](part.md) instances can be added to a given `Message` instance by
calling `->addPart($part)`

An array with all [Part](part.md) instances in the `Message` is returned from
the method `getParts()`. The `Part` instances can then be modified on
retrieveal, as they are stored in the array as references. If parts are added
to the array or the sequence is changed, the array needs to be passed back to
the `Message` instance by calling `setParts($partsArray)`.

The function `isMultiPart()` will return `TRUE` if more than one part is
registered with the `Message` instance; when true, the instance will generate a
multipart MIME message.

## Boundary handling

`Zend\Mime\Message` usually creates and uses its own `Zend\Mime\Mime` instance
to generate a boundary.  If you need to define the boundary or want to change
the behaviour of the `Mime` instance used by `Message`, you can create the
`Mime` instance yourself and register it with your `Message` using the
`setMime()` method; this is an atypical occurrence.

`getMime()` returns the `Mime` instance to use when rendering the message via
`generateMessage()`.

`generateMessage()` renders the `Message` content to a string.

## Parsing a string to create a Zend\\Mime\\Message object

`Zend\Mime\Message` defines a static factory for parsing MIME-compliant message
strings and returning a `Zend\Mime\Message` instance:

```php
$message = Zend\Mime\Message::createFromMessage($string, $boundary);
```

As of version 2.6.1, You may also parse a single-part message by omitting the
`$boundary` argument:

```php
$message = Zend\Mime\Message::createFromMessage($string);
```

## Available methods

`Zend\Mime\Message` contains the following methods:

- `getParts`: Get the all `Zend\Mime\Part`s in the message.
- `setParts($parts)`: Set the array of `Zend\Mime\Part`s for the message.
- `addPart(Zend\Mime\Part $part)`: Append a new `Zend\Mime\Part` to the
  message.
- `isMultiPart`: Check if the message needs to be sent as a multipart MIME
  message.
- `setMime(Zend\Mime\Mime $mime)`: Set a custom `Zend\Mime\Mime` object for the
  message.
- `getMime`: Get the `Zend\Mime\Mime` object for the message.
- `generateMessage($EOL = Zend\Mime\Mime::LINEEND)`: Generate a MIME-compliant
  message from the current configuration.
- `getPartHeadersArray($partnum)`: Get the headers of a given part as an array.
- `getPartHeaders($partnum, $EOL = Zend\Mime\Mime::LINEEND)`: Get the headers
  of a given part as a string.
- `getPartContent($partnum, $EOL = Zend\Mime\Mime::LINEEND)`: Get the encoded
  content of a given part as a string.

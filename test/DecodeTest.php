<?php
/**
 * @see       https://github.com/zendframework/zend-mime for the canonical source repository
 * @copyright Copyright (c) 2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-mime/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\Mime;

use PHPUnit\Framework\TestCase;
use Zend\Mail\Headers;
use Zend\Mime\Decode;
use Zend\Mail\Storage\Message;
use Zend\Mime\Exception\InvalidArgumentException;

class DecodeTest extends TestCase
{
    public function testDecodeMessageWithoutHeaders()
    {
        $text = 'This is a message body';

        Decode::splitMessage($text, $headers, $body);

        self::assertInstanceOf(Headers::class, $headers);
        self::assertSame($text, $body);
    }

    public function testSplitHeaderField()
    {
        $example = <<<EOD
From: "Ozan Akman <ozan-akman@example.com>
To: "Mary Smith" <mary@x.test>
CC: John Doe <jdoe@machine.example>, <boss@nil.test>
Date: Tue, 1 Jul 2003 10:52:37 +0200
Message-ID: <5678.21-Nov-1997@example.com>

Hi everyone, this is a test.
EOD;
        $message = new Message(['raw' => $example]);

        // Test single
        self::assertEquals('Mary Smith <mary@x.test>', $message->getHeaderField('To'));

        // Test multiple
        self::assertEquals("John Doe <jdoe@machine.example>,\r\n boss@nil.test", $message->getHeaderField('Cc'));

        // Test malformed address
        self::expectException(InvalidArgumentException::class);
        $message->getHeaderField('From');

    }
}

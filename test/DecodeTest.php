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

class DecodeTest extends TestCase
{
    public function testDecodeMessageWithoutHeaders()
    {
        $text = 'This is a message body';

        Decode::splitMessage($text, $headers, $body);

        self::assertInstanceOf(Headers::class, $headers);
        self::assertSame($text, $body);
    }
}

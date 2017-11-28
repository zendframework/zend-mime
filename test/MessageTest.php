<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mime;

use PHPUnit\Framework\TestCase;
use Zend\Mime;
use Zend\Mime\Message;

/**
 * @group      Zend_Mime
 */
class MessageTest extends TestCase
{
    public function testMultiPart()
    {
        $msg = new Mime\Message();  // No Parts
        $this->assertFalse($msg->isMultiPart());
    }

    public function testSetGetParts()
    {
        $msg = new Mime\Message();  // No Parts
        $p = $msg->getParts();
        $this->assertInternalType('array', $p);
        $this->assertEmpty($p);

        $p2 = [];
        $p2[] = new Mime\Part('This is a test');
        $p2[] = new Mime\Part('This is another test');
        $msg->setParts($p2);
        $p = $msg->getParts();
        $this->assertInternalType('array', $p);
        $this->assertCount(2, $p);
    }

    public function testGetMime()
    {
        $msg = new Mime\Message();  // No Parts
        $m = $msg->getMime();
        $this->assertInstanceOf('Zend\\Mime\\Mime', $m);

        $msg = new Mime\Message();  // No Parts
        $mime = new Mime\Mime('1234');
        $msg->setMime($mime);
        $m2 = $msg->getMime();
        $this->assertInstanceOf('Zend\\Mime\\Mime', $m2);
        $this->assertEquals('1234', $m2->boundary());
    }

    public function testGenerate()
    {
        $msg = new Mime\Message();  // No Parts
        $p1 = new Mime\Part('This is a test');
        $p2 = new Mime\Part('This is another test');
        $msg->addPart($p1);
        $msg->addPart($p2);
        $res = $msg->generateMessage();
        $mime = $msg->getMime();
        $boundary = $mime->boundary();
        $p1 = strpos($res, $boundary);
        // $boundary must appear once for every mime part
        $this->assertNotFalse($p1);
        if ($p1) {
            $p2 = strpos($res, $boundary, $p1 + strlen($boundary));
            $this->assertNotFalse($p2);
        }
        // check if the two test messages appear:
        $this->assertContains('This is a test', $res);
        $this->assertContains('This is another test', $res);
        // ... more in ZMailTest
    }

    /**
     * check if decoding a string into a \Zend\Mime\Message object works
     *
     */
    public function testDecodeMimeMessage()
    {
        $text = <<<EOD
This is a message in Mime Format.  If you see this, your mail reader does not support this format.

--=_af4357ef34b786aae1491b0a2d14399f
Content-Type: application/octet-stream
Content-Transfer-Encoding: 8bit

This is a test
--=_af4357ef34b786aae1491b0a2d14399f
Content-Type: image/gif
Content-Transfer-Encoding: base64
Content-ID: <12>

This is another test
--=_af4357ef34b786aae1491b0a2d14399f--
EOD;
        $res = Mime\Message::createFromMessage($text, '=_af4357ef34b786aae1491b0a2d14399f');

        $parts = $res->getParts();
        $this->assertEquals(2, count($parts));

        $part1 = $parts[0];
        $this->assertEquals('application/octet-stream', $part1->type);
        $this->assertEquals('8bit', $part1->encoding);

        $part2 = $parts[1];
        $this->assertEquals('image/gif', $part2->type);
        $this->assertEquals('base64', $part2->encoding);
        $this->assertEquals('12', $part2->id);
    }

    /**
     * check if decoding a string into a \Zend\Mime\Message object works
     *
     */
    public function testDecodeMimeMessageNoHeader()
    {
        $text = <<<EOD
This is a MIME-encapsulated message

--=_af4357ef34b786aae1491b0a2d14399f

The original message was received at Fri, 16 Aug 2013 00:00:48 -0700
from localhost.localdomain [127.0.0.1]
End content

--=_af4357ef34b786aae1491b0a2d14399f
Content-Type: image/gif

This is a test
--=_af4357ef34b786aae1491b0a2d14399f--
EOD;
        $res = Mime\Message::createFromMessage($text, '=_af4357ef34b786aae1491b0a2d14399f');

        $parts = $res->getParts();
        $this->assertEquals(2, count($parts));

        $part1 = $parts[0];
        $part1Content = $part1->getRawContent();
        $this->assertContains('The original message', $part1Content);
        $this->assertContains('End content', $part1Content);

        $part2 = $parts[1];
        $this->assertEquals('image/gif', $part2->type);
    }

    /**
     * Check if decoding a string that is not a multipart message works
     */
    public function testDecodeNonMultipartMimeMessage()
    {
        $text = <<<EOD
Content-Type: image/gif

This is a test
EOD;
        $res = Mime\Message::createFromMessage($text);

        $parts = $res->getParts();
        $this->assertEquals(1, count($parts));

        $part1 = $parts[0];
        $part1Content = $part1->getRawContent();
        $this->assertEquals('This is a test', $part1Content);
        $this->assertEquals('image/gif', $part1->type);
    }

    public function testNonMultipartMessageShouldNotRemovePartFromMessage()
    {
        $message = new Mime\Message();  // No Parts
        $part    = new Mime\Part('This is a test');
        $message->addPart($part);
        $message->generateMessage();

        $parts = $message->getParts();
        $test  = current($parts);
        $this->assertSame($part, $test);
    }

    /**
     * @group ZF2-5962
     */
    public function testPassEmptyArrayIntoSetPartsShouldReturnEmptyString()
    {
        $mimeMessage = new Mime\Message();
        $mimeMessage->setParts([]);

        $this->assertEquals('', $mimeMessage->generateMessage());
    }

    public function testDuplicatePartAddedWillThrowException()
    {
        $this->expectException(Mime\Exception\InvalidArgumentException::class);

        $message = new Mime\Message();
        $part    = new Mime\Part('This is a test');
        $message->addPart($part);
        $message->addPart($part);
    }

    public function testFromStringWithCrlfAndRfc2822FoldedHeaders()
    {
        // This is a fixture as provided by many mailservers
        // e.g. cyrus or dovecot
        $eol = "\r\n";
        $fixture = 'This is a MIME-encapsulated message' . $eol . $eol
            . '--=_af4357ef34b786aae1491b0a2d14399f' . $eol
            . 'Content-Type: text/plain' . $eol
            . 'Content-Disposition: attachment;' . $eol
            . "\t" . 'filename="test.txt"' . $eol // Valid folding
            . $eol
            . 'This is a test' . $eol
            . '--=_af4357ef34b786aae1491b0a2d14399f--';

        $message = Message::createFromMessage($fixture, '=_af4357ef34b786aae1491b0a2d14399f', $eol);
        $parts = $message->getParts();

        $this->assertEquals(1, count($parts));
        $this->assertEquals('attachment; filename="test.txt"', $parts[0]->getDisposition());
    }
}

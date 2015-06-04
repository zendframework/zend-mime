<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mime;

use Zend\Mime;

/**
 * @group      Zend_Mime
 */
class MessageTest extends \PHPUnit_Framework_TestCase
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

        $p2 = array();
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
        $mimeMessage->setParts(array());

        $this->assertEquals('', $mimeMessage->generateMessage());
    }

    public static function dataTestDecodeSplitMessage()
    {
        $headerpart = <<<EOD
To: foo@example.com
Subject: bar
Date: Sun, 01 Jan 2000 00:00:00 +0000
From: baz@example.com
Content-Type: text/plain
Message-ID: <aaaaa@mail.example.com>
EOD;

        $bodypart = <<<EOD
This is
the body
EOD;
        return array(array($headerpart, $bodypart));
    }

    /**
     * @dataProvider dataTestDecodeSplitMessage
     */
    public function testDecodeSplitMessage_lf($headerpart, $bodypart)
    {
        // Decode::splitMessage normal usage
        $headers = null;
        $body = null;
        Mime\Decode::splitMessage($headerpart."\n\n".$bodypart, $headers, $body);
        $this->assertInstanceOf('Zend\\Mail\\Headers', $headers);
        //$headers->toString() is using CRLF, so we can't do assertEquals
        $this->assertInternalType('string', $body);
        $this->assertEquals($bodypart, $body);
    }

    /**
     * @dataProvider dataTestDecodeSplitMessage
     */
    public function testDecodeSplitMessage_mixed($headerpart, $bodypart)
    {
        // Decode::splitMessage support mixed EOL
        $headers = null;
        $body = null;
        Mime\Decode::splitMessage($headerpart."\r\n\n".$bodypart, $headers, $body);
        $this->assertInstanceOf('Zend\Mail\Headers', $headers);
        $this->assertInternalType('string', $body);
        $this->assertEquals($bodypart, $body);
    }

    /**
     * @dataProvider dataTestDecodeSplitMessage
     */
    public function testDecodeSplitMessage_drop($headerpart, $bodypart)
    {
        // Decode::splitMessage drop first line (zf2-372)
        // postfix sometimes add an invalid header on first line
        $headers = null;
        $body = null;
        Mime\Decode::splitMessage("From foo@example.com  Sun Jan 01 00:00:00 2000\n".$headerpart."\n\n".$bodypart, $headers, $body);
        $this->assertInstanceOf('Zend\Mail\Headers', $headers);
        $this->assertInternalType('string', $body);
        $this->assertEquals($bodypart, $body);
    }

    /**
     * @dataProvider dataTestDecodeSplitMessage
     */
    public function testDecodeSplitMessage_bad($headerpart, $bodypart)
    {
        //only one EOL -> "This is" isn't an header
        $this->setExpectedException('Zend\Mail\Exception\RuntimeException');
        Mime\Decode::splitMessage($headerpart."\n".$bodypart, $headers, $body);
    }
}

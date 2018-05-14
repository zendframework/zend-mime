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

/**
 * @group      Zend_Mime
 */
class MimeTest extends TestCase
{
    // @codingStandardsIgnoreStart
    /**
     * Stores the original set timezone
     *
     * @var string
     */
    private $_originaltimezone;
    // @codingStandardsIgnoreEnd

    /**
     * Setup environment
     */
    public function setUp()
    {
        $this->_originaltimezone = date_default_timezone_get();
    }

    /**
     * Tear down environment
     */
    public function tearDown()
    {
        date_default_timezone_set($this->_originaltimezone);
    }

    public function testBoundary()
    {
        // check boundary for uniqueness
        $m1 = new Mime\Mime();
        $m2 = new Mime\Mime();
        $this->assertNotEquals($m1->boundary(), $m2->boundary());

        // check instantiating with arbitrary boundary string
        $myBoundary = 'mySpecificBoundary';
        $m3         = new Mime\Mime($myBoundary);
        $this->assertEquals($m3->boundary(), $myBoundary);
    }

    // @codingStandardsIgnoreStart
    public function testIsPrintable_notPrintable()
    {
        // @codingStandardsIgnoreEnd
        $this->assertFalse(Mime\Mime::isPrintable('Test with special chars: �����'));
    }

    // @codingStandardsIgnoreStart
    public function testIsPrintable_isPrintable()
    {
        // @codingStandardsIgnoreEnd
        $this->assertTrue(Mime\Mime::isPrintable('Test without special chars'));
    }

    public function testQP()
    {
        $text = "This is a cool Test Text with special chars: ����\n"
              . "and with multiple lines���� some of the Lines are long, long"
              . ", long, long, long, long, long, long, long, long, long, long"
              . ", long, long, long, long, long, long, long, long, long, long"
              . ", long, long, long, long, long, long, long, long, long, long"
              . ", long, long, long, long and with ����";

        $qp = Mime\Mime::encodeQuotedPrintable($text);
        $this->assertEquals(quoted_printable_decode($qp), $text);
    }

    public function testQuotedPrintableNoDotAtBeginningOfLine()
    {
        $text = str_repeat('a', Mime\Mime::LINELENGTH) . '.bbb';
        $qp = Mime\Mime::encodeQuotedPrintable($text);

        $expected = str_repeat('a', Mime\Mime::LINELENGTH) . "=\n=2Ebbb";

        $this->assertEquals($expected, $qp);
    }

    public function testQuotedPrintableSpacesAndDots()
    {
        $text = str_repeat(' ', Mime\Mime::LINELENGTH) . str_repeat('.', Mime\Mime::LINELENGTH);
        $qp = Mime\Mime::encodeQuotedPrintable($text);

        $expected = str_repeat(' ', Mime\Mime::LINELENGTH - 1)
            . "=20=\n=2E"
            . str_repeat('.', Mime\Mime::LINELENGTH - 1);

        $this->assertEquals($expected, $qp);
    }

    public function testQuotedPrintableDoesNotBreakOctets()
    {
        $text = str_repeat('a', Mime\Mime::LINELENGTH - 2) . '=.bbb';
        $qp = Mime\Mime::encodeQuotedPrintable($text);

        $expected = str_repeat('a', Mime\Mime::LINELENGTH - 2) . "=\n=3D.bbb";

        $this->assertEquals($expected, $qp);
    }

    public function testBase64()
    {
        $content = str_repeat("\x88\xAA\xAF\xBF\x29\x88\xAA\xAF\xBF\x29\x88\xAA\xAF", 4);
        $encoded = Mime\Mime::encodeBase64($content);
        $this->assertEquals($content, base64_decode($encoded));
    }

    public function testZf1058WhitespaceAtEndOfBodyCausesInfiniteLoop()
    {
        $text   = "my body\r\n\r\n...after two newlines\r\n ";
        $result = quoted_printable_decode(Mime\Mime::encodeQuotedPrintable($text));
        $this->assertContains("my body\r\n\r\n...after two newlines", $result, $result);
    }

    /**
     * @group        ZF-1688
     * @dataProvider dataTestEncodeMailHeaderQuotedPrintable
     */
    public function testEncodeMailHeaderQuotedPrintable($str, $charset, $result)
    {
        $this->assertEquals($result, Mime\Mime::encodeQuotedPrintableHeader($str, $charset));
    }

    public static function dataTestEncodeMailHeaderQuotedPrintable()
    {
        // @codingStandardsIgnoreStart
        return [
            ["äöü", "UTF-8", "=?UTF-8?Q?=C3=A4=C3=B6=C3=BC?="],
            ["äöü ", "UTF-8", "=?UTF-8?Q?=C3=A4=C3=B6=C3=BC?="],
            ["Gimme more €", "UTF-8", "=?UTF-8?Q?Gimme=20more=20=E2=82=AC?="],
            ["Alle meine Entchen schwimmen in dem See, schwimmen in dem See, Köpfchen in das Wasser, Schwänzchen in die Höh!", "UTF-8", "=?UTF-8?Q?Alle=20meine=20Entchen=20schwimmen=20in=20dem=20See=2C=20?=\n =?UTF-8?Q?schwimmen=20in=20dem=20See=2C=20K=C3=B6pfchen=20in=20das=20?=\n =?UTF-8?Q?Wasser=2C=20Schw=C3=A4nzchen=20in=20die=20H=C3=B6h!?="],
            ["ääääääääääääääääääääääääääääääääää", "UTF-8", "=?UTF-8?Q?=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4?="],
            ["A0", "UTF-8", "=?UTF-8?Q?A0?="],
            ["äääääääääääääää ä", "UTF-8", "=?UTF-8?Q?=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=20?=\n =?UTF-8?Q?=C3=A4?="],
            ["äääääääääääääää äääääääääääääää", "UTF-8", "=?UTF-8?Q?=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=20?=\n =?UTF-8?Q?=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4?="],
            ["ä äääääääääääääää", "UTF-8", "=?UTF-8?Q?=C3=A4=20=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4?="],
        ];
        // @codingStandardsIgnoreEnd
    }

    /**
     * @group        ZF-1688
     * @dataProvider dataTestEncodeMailHeaderBase64
     */
    public function testEncodeMailHeaderBase64($str, $charset, $result)
    {
        $this->assertEquals($result, Mime\Mime::encodeBase64Header($str, $charset));
    }

    public static function dataTestEncodeMailHeaderBase64()
    {
        return [
            ["äöü", "UTF-8", "=?UTF-8?B?w6TDtsO8?="],
            // @codingStandardsIgnoreStart
            ["Alle meine Entchen schwimmen in dem See, schwimmen in dem See, Köpfchen in das Wasser, Schwänzchen in die Höh!", "UTF-8", "=?UTF-8?B?QWxsZSBtZWluZSBFbnRjaGVuIHNjaHdpbW1lbiBpbiBkZW0gU2VlLCBzY2h3?=
 =?UTF-8?B?aW1tZW4gaW4gZGVtIFNlZSwgS8O2cGZjaGVuIGluIGRhcyBXYXNzZXIsIFNj?=
 =?UTF-8?B?aHfDpG56Y2hlbiBpbiBkaWUgSMO2aCE=?="],
            // @codingStandardsIgnoreEnd
        ];
    }

    /**
     * base64 chunk are 4 chars long
     * try to encode/decode with 4 line length
     * @dataProvider dataTestEncodeMailHeaderBase64wrap
     */
    public function testEncodeMailHeaderBase64wrap($str)
    {
        $this->assertEquals($str, Mime\Decode::decodeQuotedPrintable(Mime\Mime::encodeBase64Header($str, "UTF-8", 20)));
        $this->assertEquals($str, Mime\Decode::decodeQuotedPrintable(Mime\Mime::encodeBase64Header($str, "UTF-8", 21)));
        $this->assertEquals($str, Mime\Decode::decodeQuotedPrintable(Mime\Mime::encodeBase64Header($str, "UTF-8", 22)));
        $this->assertEquals($str, Mime\Decode::decodeQuotedPrintable(Mime\Mime::encodeBase64Header($str, "UTF-8", 23)));
    }

    public static function dataTestEncodeMailHeaderBase64wrap()
    {
        return [
            ["äöüäöüäöüäöüäöüäöüäöü"],
            ["Alle meine Entchen schwimmen in dem See, schwimmen in dem See, "
                . "Köpfchen in das Wasser, Schwänzchen in die Höh!"]
        ];
    }

    public function testFromMessageMultiPart()
    {
        $message = Mime\Message::createFromMessage(
            '--089e0141a1902f83ee04e0a07b7a'."\r\n"
            .'Content-Type: multipart/alternative; boundary=089e0141a1902f83e904e0a07b78'."\r\n"
            ."\r\n"
            .'--089e0141a1902f83e904e0a07b78'."\r\n"
            .'Content-Type: text/plain; charset=UTF-8'."\r\n"
            ."\r\n"
            .'Foo'."\r\n"
            ."\r\n"
            .'--089e0141a1902f83e904e0a07b78'."\r\n"
            .'Content-Type: text/html; charset=UTF-8'."\r\n"
            ."\r\n"
            .'<p>Foo</p>'."\r\n"
            ."\r\n"
            .'--089e0141a1902f83e904e0a07b78--'."\r\n"
            .'--089e0141a1902f83ee04e0a07b7a'."\r\n"
            .'Content-Type: image/png; name="1.png"'."\r\n"
            .'Content-Disposition: attachment; filename="1.png"'."\r\n"
            .'Content-Transfer-Encoding: base64'."\r\n"
            .'X-Attachment-Id: barquux'."\r\n"
            ."\r\n"
            .'Zm9vCg=='."\r\n"
            .'--089e0141a1902f83ee04e0a07b7a--',
            '089e0141a1902f83ee04e0a07b7a'
        );
        $this->assertSame(2, count($message->getParts()));
    }

    public static function dataTestFromMessageDecode()
    {
        return [
            ['äöü', 'quoted-printable', '=C3=A4=C3=B6=C3=BC'],
            // @codingStandardsIgnoreStart
            ['Alle meine Entchen schwimmen in dem See, schwimmen in dem See, Köpfchen in das Wasser, Schwänzchen in die Höh!', 'quoted-printable', 'Alle meine Entchen schwimmen in dem See, schwimmen in dem See, K=C3=B6pfche=
n in das Wasser, Schw=C3=A4nzchen in die H=C3=B6h!'],
            // @codingStandardsIgnoreEnd
            ['foobar', 'base64', 'Zm9vYmFyCg=='],
        ];
    }

    /**
     * @dataProvider dataTestFromMessageDecode
     */
    public function testFromMessageDecode($input, $encoding, $result)
    {
        $parts = Mime\Message::createFromMessage(
            '--089e0141a1902f83ee04e0a07b7a'."\r\n"
            .'Content-Type: text/plain; charset=UTF-8'."\r\n"
            .'Content-Transfer-Encoding: '.$encoding."\r\n"
            ."\r\n"
            .$result."\r\n"
            .'--089e0141a1902f83ee04e0a07b7a--',
            '089e0141a1902f83ee04e0a07b7a'
        )->getParts();
        $this->assertSame($input."\n", $parts[0]->getRawContent());
    }

    /**
     * @group ZF-1688
     */
    public function testLineLengthInQuotedPrintableHeaderEncoding()
    {
        $subject = "Alle meine Entchen schwimmen in dem See, schwimmen in dem See, "
            . "Köpfchen in das Wasser, Schwänzchen in die Höh!";
        $encoded = Mime\Mime::encodeQuotedPrintableHeader($subject, "UTF-8", 100);
        foreach (explode(Mime\Mime::LINEEND, $encoded) as $line) {
            $this->assertLessThanOrEqual(
                100,
                strlen($line),
                "Line '" . $line . "' is " . strlen($line) . " chars long, only 100 allowed."
            );
        }
        $encoded = Mime\Mime::encodeQuotedPrintableHeader($subject, "UTF-8", 40);
        foreach (explode(Mime\Mime::LINEEND, $encoded) as $line) {
            $this->assertLessThanOrEqual(
                40,
                strlen($line),
                "Line '" . $line . "' is " . strlen($line) . " chars long, only 40 allowed."
            );
        }
    }

    public function dataTestCharsetDetection()
    {
        return [
            ["ASCII", "test"],
            ["ASCII", "=?ASCII?Q?test?="],
            ["UTF-8", "=?UTF-8?Q?test?="],
            ["ISO-8859-1", "=?ISO-8859-1?Q?Pr=FCfung_f=FCr?= Entwerfen von einer MIME kopfzeile"],
            ["UTF-8", "=?UTF-8?Q?Pr=C3=BCfung=20Pr=C3=BCfung?="]
        ];
    }

    /**
     * @dataProvider dataTestCharsetDetection
     */
    public function testCharsetDetection($expected, $string)
    {
        $this->assertEquals($expected, Mime\Mime::mimeDetectCharset($string));
    }
}

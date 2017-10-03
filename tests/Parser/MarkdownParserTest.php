<?php
/**
 * PostTest.php
 *
 * Created By: jonathan
 * Date: 25/09/2017
 * Time: 12:33
 */

namespace Stati\Tests\Parser;

use Stati\Parser\MarkdownParser;
use Stati\Tests\TestCase;

class MarkdownParserTest extends TestCase
{
    public function testParseWithClass()
    {
        $text = '
{: .highlight}
c

';
        $parser = new MarkdownParser();
        $parsed = $parser->text($text);
        $this->assertEquals('<p class="highlight">
c</p>', $parsed);
    }

}

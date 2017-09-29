<?php
/**
 * PostTest.php
 *
 * Created By: jonathan
 * Date: 25/09/2017
 * Time: 12:33
 */

namespace Stati\Tests;

use Stati\Parser\ContentParser;

class ContentParserTest extends TestCase
{
    public function testParseContent()
    {
        $text = '---
test:true
---

This is the content

';
        $parsed = ContentParser::parse($text);
        $this->assertEquals('
This is the content

', $parsed);
    }
}

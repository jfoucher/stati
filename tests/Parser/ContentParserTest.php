<?php
/**
 * PostTest.php
 *
 * Created By: jonathan
 * Date: 25/09/2017
 * Time: 12:33
 */

namespace Stati\Tests\Parser;

use Stati\Parser\ContentParser;
use Stati\Tests\TestCase;

class ContentParserTest extends TestCase
{
    public function testParseContentWithFrontMatter()
    {
        $text = '---
a:b
---

c

';
        $parsed = ContentParser::parse($text);
        $this->assertEquals('c', $parsed);
    }

    public function testParseContentWithoutFrontMatter()
    {
        $text = '
c

';
        $parsed = ContentParser::parse($text);
        $this->assertEquals('c', $parsed);
    }

    public function testParseEmptyContent()
    {
        $text = '';
        $parsed = ContentParser::parse($text);
        $this->assertEquals('', $parsed);
    }

    public function testParseContentWithManyFrontMatterMarkers()
    {
        $text = <<<EOL
---
a:b
---
c
---
d
---
EOL;
;
        $parsed = ContentParser::parse($text);
        $this->assertEquals('c
---
d
---', $parsed);
    }
}

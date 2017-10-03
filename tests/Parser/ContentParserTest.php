<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
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

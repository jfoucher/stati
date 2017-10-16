<?php

/*
 * This file is part of the Stati package.
 *
 * Copyright 2017 Jonathan Foucher
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
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
        $this->assertEquals('c

', $parsed);
    }

    public function testParseContentWithoutFrontMatter()
    {
        $text = '
c

';
        $parsed = ContentParser::parse($text);
        $this->assertEquals('
c

', $parsed);
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

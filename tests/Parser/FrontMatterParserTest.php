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

use Stati\Parser\FrontMatterParser;
use Stati\Tests\TestCase;

class FrontMatterParserTest extends TestCase
{
    public function testParseFrontMatter()
    {
        $text = '---
test: true
things:
    - thing1
    - thing2
    
    
---

This is the content

';
        $parsed = FrontMatterParser::parse($text);
        $this->assertEquals([
            'test' => true,
            'things' =>
                [
                    0 => 'thing1',
                    1 => 'thing2',
                ],
        ], $parsed);
    }

    public function testNoFrontMatterShouldReturnEmptyArray()
    {
        $parsed = FrontMatterParser::parse('none');
        $this->assertEquals([], $parsed);
    }

    public function testManyFrontMatterMarkersShouldReturnTop()
    {
        $text = <<<EOL
---
a: b
---
c: d
---
e
---
f
EOL;

        $parsed = FrontMatterParser::parse($text);
        $this->assertEquals(['a' => 'b'], $parsed);
    }

    public function testEmptyFrontMatterShouldReturnEmptyArray()
    {
        $text = <<<EOL
---

---

text
EOL;

        $parsed = FrontMatterParser::parse($text);

        $this->assertEquals([], $parsed);
    }

    public function testInvalidFrontMatterShouldReturnEmptyArray()
    {
        $text = <<<EOL
---
ghgh//gh-:- ghh ghhhh//

---

text
EOL;

        $parsed = FrontMatterParser::parse($text);

        $this->assertEquals([], $parsed);
    }
}

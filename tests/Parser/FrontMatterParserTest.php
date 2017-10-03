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

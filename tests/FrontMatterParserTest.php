<?php
/**
 * PostTest.php
 *
 * Created By: jonathan
 * Date: 25/09/2017
 * Time: 12:33
 */

namespace Stati\Tests;

use Stati\Parser\FrontMatterParser;

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
}
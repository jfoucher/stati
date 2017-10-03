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

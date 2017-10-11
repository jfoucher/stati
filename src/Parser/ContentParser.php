<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
 */

namespace Stati\Parser;

use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;

class ContentParser
{
    public static function parse($text)
    {
        $regexp = '~\A\s*---\s*\n(.*?\n?)^((---|\.\.\.)\s*$\n?)(.*)~ms';
        if (preg_match_all($regexp, $text, $matches, PREG_SET_ORDER, 0)) {
            if (isset($matches[0]) && isset($matches[0][4])) {
                return $matches[0][4];
            }
            return $text;
        }
        return $text;
    }
}

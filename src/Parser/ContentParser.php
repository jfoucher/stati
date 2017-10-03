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
        $split = preg_split("/[\n]*[-]{3}/", $text, 2, PREG_SPLIT_NO_EMPTY);

        if (is_array($split) && count($split) > 1) {
            return trim($split[1]);
        }
        if (is_array($split) && count($split)) {
            return trim($split[0]);
        }
        return '';
    }
}

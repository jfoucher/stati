<?php
/**
 * FrontMatterParser.php
 *
 * Created By: jonathan
 * Date: 23/09/2017
 * Time: 00:34
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
            return $split[1];
        }
        if (is_array($split) && count($split)) {
            return $split[0];
        }
        return '';

    }
}
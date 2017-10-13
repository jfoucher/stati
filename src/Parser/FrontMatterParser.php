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

class FrontMatterParser
{
    public static function parse($text)
    {
        if (strpos($text, "---") === false) {
            return [];
        }

        $regexp = '~\A\s*---\s*\n(.*?\n?)^((---|\.\.\.)\s*$\n?)(.*)~ms';
        if (preg_match_all($regexp, $text, $matches, PREG_SET_ORDER, 0)) {
            if (isset($matches[0]) && isset($matches[0][1])) {
                $yaml = Yaml::parse($matches[0][1]);
                if (!$yaml || !is_array($yaml)) {
                    return [];
                }
                return $yaml;
            }
            return [];
        }
        return [];
    }
}

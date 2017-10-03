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

use Symfony\Component\Yaml\Yaml;

class FrontMatterParser
{
    public static function parse($text)
    {
        $split = preg_split("/[\n]*[-]{3}/", $text, 2, PREG_SPLIT_NO_EMPTY);

        if (count($split) > 1) {
            try {
                // Using symfony's YAML parser
                // we can use trim here because we only remove white space
                // at the beginning (first should not have any) and at the end (insignificant)

                return Yaml::parse($split[0]);
            } catch (\Exception $err) {
                // This is not YAML
                return [];
            }
        }
        return [];
    }
}

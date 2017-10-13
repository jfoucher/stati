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

use Symfony\Component\Yaml\Yaml as BaseYaml;

class Yaml extends BaseYaml
{
    public static function parse($input, $flags = 0)
    {
        if (!$input) {
            return [];
        }
        if (function_exists('yaml_parse')) {
            return yaml_parse("---\n".$input);
        }
        return parent::parse($input, $flags);
    }
}

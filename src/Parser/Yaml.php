<?php
/**
 * Yaml.php
 *
 * Created By: jonathan
 * Date: 13/10/2017
 * Time: 10:44
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
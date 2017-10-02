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
use Symfony\Component\Yaml\Exception\ParseException;

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
                $val = trim($split[0]);
                if (strlen($val) === 0) {
                    return [];
                }
                return Yaml::parse($val);
            } catch (InvalidArgumentException $e) {
                // This is not YAML
                return [];
            } catch (ParseException $err) {
                return [];
            }
        }
        return [];
    }
}

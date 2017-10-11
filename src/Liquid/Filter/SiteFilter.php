<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
 */

namespace Stati\Liquid\Filter;

use Liquid\Liquid;
use Stati\Parser\MarkdownParser;

class SiteFilter
{
    /**
     * Escapes an xml string
     *
     * @param string $input
     *
     * @return string
     */
    public static function xml_escape($input)
    {
        return htmlentities($input);
    }

    /**
     * Counts the number of words in a string
     *
     * @param string $input
     *
     * @return string
     */

    public static function number_of_words($input)
    {
        return str_word_count(strip_tags($input), 0);
    }

    public static function tojson($input)
    {
        return json_encode($input);
    }

    public static function jsonify($input)
    {
        return json_encode($input);
    }

    public function array_to_sentence_string($input)
    {
        if (is_array($input)) {
            return implode(' ', $input);
        }
        return $input;
    }

    public function where($input, $field, $value)
    {
        if (!is_array($input)) {
            return $input;
        }

        return array_filter($input, function ($item) use ($field, $value) {
            if (is_array($item) && isset($item[$field]) && $item[$field] === $value) {
                return true;
            }
            if (is_object($item) && $item->{$field} === $value) {
                return true;
            }
            return false;
        });
    }

    /**
     * Joins elements of an array with a given character between them
     *
     * @param array|\Traversable $input
     * @param string $glue
     *
     * @return string
     */
    public static function join($input, $glue = ' ')
    {
        if ($input instanceof \Traversable) {
            $str = '';
            foreach ($input as $elem) {
                if ($str) {
                    $str .= $glue;
                }
                $str .= $elem;
            }
            return $str;
        }
        return is_array($input) ? implode($glue, Liquid::arrayFlatten($input)) : $input;
    }

    /**
     * Parses markdown text and returns html
     *
     * @param string $text
     * @return string
     */
    public static function markdownify($text)
    {
        $parser = new MarkdownParser();
        return $parser->text($text);
    }
}

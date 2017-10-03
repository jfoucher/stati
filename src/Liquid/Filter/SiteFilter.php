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
}

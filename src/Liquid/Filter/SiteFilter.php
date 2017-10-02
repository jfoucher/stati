<?php
/**
 * SiteFilter.php
 *
 * Created By: jonathan
 * Date: 28/09/2017
 * Time: 13:33
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

    public function array_to_sentence_string($input) {
        if (is_array($input)) {
            return implode(' ', $input);
        }
        return $input;
    }
}

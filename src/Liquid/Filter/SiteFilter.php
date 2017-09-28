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
}
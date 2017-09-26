<?php
/**
 * Reader.php
 *
 * Created By: jonathan
 * Date: 26/09/2017
 * Time: 22:09
 */

namespace Stati\Reader;


class Reader
{

    protected $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }
}
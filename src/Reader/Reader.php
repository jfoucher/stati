<?php
/**
 * Reader.php
 *
 * Created By: jonathan
 * Date: 26/09/2017
 * Time: 22:09
 */

namespace Stati\Reader;

use Stati\Site\Site;

class Reader
{
    protected $site;

    protected $config;

    public function __construct(Site $site, $config = [])
    {
        $this->site = $site;
        $this->config = $config;
    }
}

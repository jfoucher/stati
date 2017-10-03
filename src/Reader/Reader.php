<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
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

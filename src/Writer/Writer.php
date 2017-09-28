<?php
/**
 * Writer.php
 *
 * Created By: jonathan
 * Date: 26/09/2017
 * Time: 23:59
 */

namespace Stati\Writer;

use Symfony\Component\Filesystem\Filesystem;
use Stati\Site\Site;

class Writer
{
    /**
     * @var Site
     */
    protected $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }
}

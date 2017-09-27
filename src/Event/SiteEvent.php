<?php
/**
 * SiteWillResetEvent.php
 *
 * Created By: jonathan
 * Date: 27/09/2017
 * Time: 20:33
 */

namespace Stati\Event;

use Symfony\Component\EventDispatcher\Event;
use Stati\Site\Site;

class SiteEvent extends Event
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
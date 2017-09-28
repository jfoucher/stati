<?php
/**
 * SiteWillResetEvent.php
 *
 * Created By: jonathan
 * Date: 27/09/2017
 * Time: 20:33
 */

namespace Stati\Event;

use Stati\Site\Site;
use Stati\Event\SiteEvent;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class DidResetSiteEvent
 * Event triggered after site is reset
 *
 * @package Stati\Event
 *
 */
class DidResetSiteEvent extends SiteEvent
{
    /**
     * The filesystem used to clear the destination directory
     * @var Filesystem
     */
    protected $fs;

    public function __construct(Site $site, Filesystem $fs)
    {
        parent::__construct($site);
        $this->fs = $fs;
    }
}

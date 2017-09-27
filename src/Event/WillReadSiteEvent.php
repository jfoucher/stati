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
 * Class WillReadSiteEvent
 * Event triggered before site is read
 *
 * @package Stati\Event
 *
 */
class WillReadSiteEvent extends SiteEvent
{

}
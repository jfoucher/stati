<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
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

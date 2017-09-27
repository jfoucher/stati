<?php
/**
 * Paginator.php
 *
 * Created By: jonathan
 * Date: 27/09/2017
 * Time: 20:08
 */

namespace Stati\Plugin\Paginator;

use Stati\Plugin\Plugin;
use Stati\Site\Site;
use Stati\Site\SiteEvents;

class Paginator extends Plugin
{
    public static function getSubscribedEvents()
    {
        return array(
            SiteEvents::WILL_READ_SITE => 'onBeforeSiteRead',
        );
    }

    public function onBeforeSiteRead()
    {

    }
}
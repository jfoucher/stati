<?php
/**
 * Plugin.php
 *
 * Created By: jonathan
 * Date: 27/09/2017
 * Time: 20:09
 */

namespace Stati\Plugin;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Plugin implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array();
    }
}

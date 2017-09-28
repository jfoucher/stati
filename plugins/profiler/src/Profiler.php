<?php

namespace Stati\Plugin\Profiler;

use Stati\Event\ConsoleOutputEvent;
use Stati\Event\SiteEvent;
use Stati\Plugin\Plugin;
use Stati\Site\SiteEvents;
use Symfony\Component\Stopwatch\Stopwatch;

class Profiler extends Plugin
{
    protected $name = 'profiler';

    /**
     * @var Stopwatch
     */
    private $stopwatch;

    public static function getSubscribedEvents()
    {
        return array(
            SiteEvents::WILL_PROCESS_SITE => 'onProcessStart',
            SiteEvents::DID_PROCESS_SITE => 'onSiteEvent',
            SiteEvents::WILL_RESET_SITE => 'onSiteEvent',
            SiteEvents::DID_RESET_SITE => 'onSiteEvent',
            SiteEvents::WILL_READ_SITE => 'onSiteEvent',
            SiteEvents::DID_READ_SITE => 'onSiteEvent',
            SiteEvents::WILL_GENERATE_SITE => 'onSiteEvent',
            SiteEvents::DID_GENERATE_SITE => 'onSiteEvent',
            SiteEvents::WILL_RENDER_SITE => 'onSiteEvent',
            SiteEvents::DID_RENDER_SITE => 'onSiteEvent',
            SiteEvents::WILL_WRITE_SITE => 'onSiteEvent',
            SiteEvents::DID_WRITE_SITE => 'onSiteEvent',
            SiteEvents::WILL_READ_STATIC_FILES => 'onSiteEvent',
            SiteEvents::DID_READ_STATIC_FILES => 'onSiteEvent',
            SiteEvents::WILL_READ_COLLECTIONS => 'onSiteEvent',
            SiteEvents::DID_READ_COLLECTIONS => 'onSiteEvent',
            SiteEvents::WILL_READ_PAGES => 'onSiteEvent',
            SiteEvents::DID_READ_PAGES => 'onSiteEvent',
            SiteEvents::WILL_RENDER_STATIC_FILES => 'onSiteEvent',
            SiteEvents::DID_RENDER_STATIC_FILES => 'onSiteEvent',
            SiteEvents::WILL_RENDER_COLLECTIONS => 'onSiteEvent',
            SiteEvents::DID_RENDER_COLLECTIONS => 'onSiteEvent',
            SiteEvents::WILL_RENDER_PAGES => 'onSiteEvent',
            SiteEvents::DID_RENDER_PAGES => 'onSiteEvent',
            SiteEvents::WILL_WRITE_STATIC_FILES => 'onSiteEvent',
            SiteEvents::DID_WRITE_STATIC_FILES => 'onSiteEvent',
            SiteEvents::WILL_WRITE_COLLECTIONS => 'onSiteEvent',
            SiteEvents::DID_WRITE_COLLECTIONS => 'onSiteEvent',
            SiteEvents::WILL_WRITE_PAGES => 'onSiteEvent',
            SiteEvents::DID_WRITE_PAGES => 'onSiteEvent',
        );
    }

    public function onProcessStart(SiteEvent $event, $eventName)
    {
        $this->stopwatch = new Stopwatch();
        $this->stopwatch->start('process');
    }

    public function onSiteEvent(SiteEvent $event, $eventName)
    {
        $site = $event->getSite();
        $eventName = explode('.', $eventName)[1];

        if (preg_match('/^([a-z]+)_(.+)$/', $eventName, $matches)) {
            $action = $matches[1];
            $name = $matches[2];
            if ($action === 'will') {
                $this->stopwatch->start($name);
            }
            if ($action === 'did') {
                $stop = $this->stopwatch->stop($name);
            }
        }

        if (isset($stop) && isset($name)) {
            if ($stop->getDuration() > 1) {
                $time = number_format($stop->getDuration()/1000, 3);
                $site->getDispatcher()->dispatch(SiteEvents::CONSOLE_OUTPUT, new ConsoleOutputEvent('text', ['Site '.$name.' took '.$time.'s']));
            }
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}

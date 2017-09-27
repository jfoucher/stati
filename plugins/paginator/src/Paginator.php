<?php
/**
 * Paginator.php
 *
 * Created By: jonathan
 * Date: 27/09/2017
 * Time: 20:08
 */

namespace Stati\Plugin\Paginator;

use Stati\Event\SiteEvent;
use Stati\Plugin\Paginator\Renderer\PaginatorRenderer;
use Stati\Plugin\Plugin;
use Stati\Site\Site;
use Stati\Site\SiteEvents;
use Stati\Plugin\Paginator\Entity\Paginator as PaginatorEntity;
use Stati\Plugin\Paginator\Writer\PaginatorWriter;

class Paginator extends Plugin
{
    protected $name = 'paginator';

    public static function getSubscribedEvents()
    {
        return array(
            SiteEvents::DID_READ_SITE => 'onAfterSiteRead',
            SiteEvents::DID_RENDER_SITE => 'onAfterSiteRender',
        );
    }

    public function onAfterSiteRead(SiteEvent $event)
    {
        $site = $event->getSite();
        if (!$site->paginate) {
            return;
        }
        $docs = $site->getPosts()->getDocs();
        usort($docs, function($a, $b) {
            return $a->getDate()->getTimestamp() > $b->getDate()->getTimestamp() ? -1 : 1;
        });
        $paginator = new PaginatorEntity($docs, $site->getConfig());
        $site->paginator = $paginator;
    }

    public function onAfterSiteRender(SiteEvent $event)
    {
        $site = $event->getSite();

        if (!$site->paginate) {
            return;
        }
        $paginatorRenderer = new PaginatorRenderer($site);
        $renderedPages = $paginatorRenderer->renderAll();
        foreach ($renderedPages as $page) {
            $site->addPage($page);
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
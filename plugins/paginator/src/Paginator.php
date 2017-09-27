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
use Stati\Plugin\Paginator\Entity\PaginatorPage;

class Paginator extends Plugin
{
    protected $name = 'paginator';

    public static function getSubscribedEvents()
    {
        return array(
            SiteEvents::DID_READ_SITE => 'onAfterSiteRead',
            SiteEvents::DID_RENDER_SITE => 'onAfterSiteRender',
            SiteEvents::DID_WRITE_SITE => 'onAfterSiteWrite',
        );
    }

    public function onAfterSiteRead(SiteEvent $event)
    {
        $site = $event->getSite();
        if ($site->paginate) {
            return;
        }
        $paginator = new PaginatorEntity($site->getPosts()->getDocs(), $site->getConfig());
        $site->paginator = $paginator;
    }

    public function onAfterSiteRender(SiteEvent $event)
    {
        $site = $event->getSite();
        $paginatorRenderer = new PaginatorRenderer($site);
        $renderedPaginator = $paginatorRenderer->renderAll();
        $site->paginator = $renderedPaginator;
    }

    public function onAfterSiteWrite(SiteEvent $event)
    {
        $site = $event->getSite();
        $paginatorRenderer = new PaginatorRenderer($site);
        $renderedPaginator = $paginatorRenderer->renderAll();
        $site->paginator = $renderedPaginator;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

}
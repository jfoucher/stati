<?php
/**
 * Paginator.php
 *
 * Created By: jonathan
 * Date: 27/09/2017
 * Time: 20:08
 */

namespace Stati\Plugin\Related;

use Stati\Event\SiteEvent;
use Stati\Event\SettingTemplateVarsEvent;
use Stati\Plugin\Plugin;
use Stati\Site\Site;
use Stati\Site\SiteEvents;
use Stati\Plugin\Paginator\Entity\Paginator as PaginatorEntity;
use Stati\Liquid\TemplateEvents;

class Related extends Plugin
{
    protected $name = 'related';

    public static function getSubscribedEvents()
    {
        return array(
            TemplateEvents::SETTING_LAYOUT_TEMPLATE_VARS => 'onSettingTemplateVars',
            TemplateEvents::SETTING_TEMPLATE_VARS => 'onSettingTemplateVars',
        );
    }

    public function onSettingTemplateVars(SettingTemplateVarsEvent $event)
    {
        $vars = $event->getVars();
        $site = $event->getSite();
        $doc = $event->getDoc();
        $vars['related_posts'] = 'TEST';
        $relatedPosts = [];
        $site->setRelatedPosts($relatedPosts);
        if (get_class($doc) === 'Stati\Entity\Post') {
            //Is a post, generate related_posts
            $title = $doc->getTitle();
            $allPosts = array_filter($site->getPosts(), function ($p) use ($title) {
                return $p->getTitle() !== $title;
            });
            usort($allPosts, function ($a, $b) use ($title) {
                return levenshtein($a->getTitle(), $title) > levenshtein($b->getTitle(), $title) ? 1 : -1;
            });
            $relatedPosts = array_slice($allPosts, 0, 10);
            $site->setRelatedPosts($relatedPosts);
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

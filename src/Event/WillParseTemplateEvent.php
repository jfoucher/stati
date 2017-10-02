<?php
/**
 * TemplateEvent.php
 *
 * Created By: jonathan
 * Date: 28/09/2017
 * Time: 00:12
 */

namespace Stati\Event;

use Liquid\Template;
use Stati\Entity\Doc;
use Symfony\Component\EventDispatcher\Event;
use Stati\Site\Site;

class WillParseTemplateEvent extends Event
{
    /**
     * @var Site
     */
    protected $site;

    /**
     * @var Template
     */
    protected $template;

    public function __construct(Site $site, Template $template)
    {
        $this->site = $site;
        $this->template = $template;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @return Template
     */
    public function getTemplate(): Template
    {
        return $this->template;
    }

}

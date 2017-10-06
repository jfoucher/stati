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
    public function getTemplate()
    {
        return $this->template;
    }
}

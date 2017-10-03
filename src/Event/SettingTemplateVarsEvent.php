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

use Stati\Entity\Doc;
use Symfony\Component\EventDispatcher\Event;
use Stati\Site\Site;

class SettingTemplateVarsEvent extends Event
{
    /**
     * @var array
     */
    protected $vars;

    /**
     * @var Site
     */
    protected $site;

    /**
     * @var Doc
     */
    protected $doc;

    public function __construct(Site $site, $vars, Doc $doc)
    {
        $this->site = $site;
        $this->vars = $vars;
        $this->doc = $doc;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @return array
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * @return Doc
     */
    public function getDoc()
    {
        return $this->doc;
    }
}

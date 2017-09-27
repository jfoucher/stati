<?php
/**
 * TemplateEvent.php
 *
 * Created By: jonathan
 * Date: 28/09/2017
 * Time: 00:12
 */

namespace Stati\Event;

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

    public function __construct(Site $site, $vars)
    {
        $this->site = $site;
        $this->vars = $vars;
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

}
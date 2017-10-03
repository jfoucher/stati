<?php
/**
 * Template.php
 *
 * Created By: jonathan
 * Date: 02/10/2017
 * Time: 19:47
 */

namespace Stati\Liquid\Template;

use Liquid\Template as BaseTemplate;
use Liquid\Cache;
use Stati\Liquid\Block\Highlight;
use Stati\Liquid\Tag\PostUrl;
use Stati\Liquid\Filter\SiteFilter;
use Stati\Liquid\Tag\Link;
use Stati\Liquid\Tag\TagInclude;

class Template extends BaseTemplate
{
    /**
     * Constructor.
     *
     * @param string $path
     * @param array|Cache $cache
     *
     * @return Template
     */
    public function __construct($path = null, $cache = null)
    {
        parent::__construct($path, $cache);
        $this->registerTag('highlight', Highlight::class);
        $this->registerTag('post_url', PostUrl::class);
        $this->registerTag('link', Link::class);
        $this->registerTag('include', TagInclude::class);
        $this->registerFilter(new SiteFilter());
    }
}
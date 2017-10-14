<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
 */

namespace Stati\Liquid\Template;

use Liquid\FileSystem\Virtual;
use Liquid\Template as BaseTemplate;
use Liquid\Cache;
use Stati\Liquid\Block\Highlight;
use Stati\Liquid\Tag\PostUrl;
use Stati\Liquid\Filter\SiteFilter;
use Stati\Liquid\Tag\Link;
use Stati\Liquid\Tag\TagInclude;
use Stati\Liquid\Filesystem\Local;

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
        if (is_dir($path)) {
            parent::__construct($path, $cache);
            $filesystem = new Local($path);
        } else {
            parent::__construct(null, $cache);
            $filesystem = new Virtual(function(){});
        }

        $this->registerTag('highlight', Highlight::class);
        $this->registerTag('post_url', PostUrl::class);
        $this->registerTag('link', Link::class);
        $this->registerTag('include', TagInclude::class);
        $this->registerFilter(new SiteFilter());

        $this->setFileSystem($filesystem);
    }
}

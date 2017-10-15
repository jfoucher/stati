<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
 */

namespace Stati\Liquid\Tag;

use Liquid\AbstractBlock;
use Liquid\Context;
use Stati\Exception\FileNotFoundException;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Finder\Finder;
use Stati\Entity\Page;

class Strip extends AbstractBlock
{
    public function render(Context $context)
    {
        return preg_replace('/\n\s*\n/', "\n", parent::render($context));
    }
}

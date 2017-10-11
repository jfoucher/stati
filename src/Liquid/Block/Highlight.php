<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
 */

namespace Stati\Liquid\Block;

use Liquid\AbstractBlock;
use Liquid\Context;
use Ramsey\Pygments\Pygments;

class Highlight extends AbstractBlock
{
    public function render(Context $context)
    {
        $pygments = new Pygments();
        $markup = explode(' ', trim($this->markup));
        $language = $markup[0];
        $options = [];
        foreach (array_slice($markup, 1) as $item) {
            $r = explode('=', $item);
            if (isset($r[1])) {
                $options[trim($r[0])] = trim($r[1]);
            } else {
                $options[trim($r[0])] = true;
            }
        }
        $nodes = $this->getNodelist();

        $texts = array_map(function ($item) use ($context) {
            if (is_string($item)) {
                return $item;
            }
            return $item->render($context);
        }, $nodes);

        return $pygments->highlight(implode('', $texts), $language, 'html', $options);
    }
}

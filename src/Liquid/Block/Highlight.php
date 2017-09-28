<?php
/**
 * Highlight.php
 *
 * Created By: jonathan
 * Date: 23/09/2017
 * Time: 00:02
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
            $options[trim($r[0])] = trim($r[1]);
        }
        $nodes = $this->getNodelist();
        return $pygments->highlight(implode('', $nodes), $language, 'html', $options);
    }
}

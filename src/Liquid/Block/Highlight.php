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
        $pygments = $context->environments[1]['pygments'];
        $language = trim($this->markup);
        $nodes = $this->getNodelist();
        return $pygments->highlight(implode('', $nodes), $language, 'html');
    }
}
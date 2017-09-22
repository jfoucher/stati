<?php
/**
 * Highlight.php
 *
 * Created By: jonathan
 * Date: 23/09/2017
 * Time: 00:02
 */

namespace Stati\LiquidBlock;

use Liquid\AbstractBlock;
use Liquid\Context;

class Highlight extends AbstractBlock
{
    public function render(Context $context)
    {
        return 'HIGHLIGHT CODE';
    }
}
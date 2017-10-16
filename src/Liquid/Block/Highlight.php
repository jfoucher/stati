<?php

/*
 * This file is part of the Stati package.
 *
 * Copyright 2017 Jonathan Foucher
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
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

        try {
            return $pygments->highlight(implode('', $texts), $language, 'html', $options);
        } catch (\Exception $err) {
            return $pygments->highlight(implode('', $texts), null, 'html', $options);
        }
    }
}

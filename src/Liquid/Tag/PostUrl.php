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

namespace Stati\Liquid\Tag;

use Liquid\AbstractTag;
use Liquid\Context;
use Stati\Exception\FileNotFoundException;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Finder\Finder;
use Stati\Entity\Post;

class PostUrl extends AbstractTag
{
    public function render(Context $context)
    {
        $post = trim($this->markup);


        $info = pathinfo($post);
        $dir = trim($info['dirname'], '/');
        if (!isset($info['extension'])) {
            $pattern = '*' . $info['basename'] . '.*';
        } else {
            $pattern = '*' . $info['filename'];
        }


        $finder = new Finder();
        $finder->depth(' <= 1')
            ->files()
            ->in('./_posts/')
            ->path($dir)
            ->name($pattern)
            ;
        if ($finder->count() === 0) {
            throw new FileNotFoundException('Could not find the post to link to');
        }
        foreach ($finder as $f) {
            $file = $f;
        }


        if (!isset($file) || $file === null) {
            return '';
        }
        $post = new Post($file, $context->get('site'));
        return $post->getUrl();
    }
}

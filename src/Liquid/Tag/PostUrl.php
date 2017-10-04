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
        $pattern = '*'.$post.'.*';

        if (!pathinfo($post, PATHINFO_EXTENSION)) {
            $finder = new Finder();
            $finder->depth(' <= 1')
                ->files()
                ->in('./_posts/')
                ->name($pattern)
                ;
            if ($finder->count() === 0) {
                throw new FileNotFoundException('Could not find the post to link to');
            }
            foreach ($finder as $f) {
                $file = $f;
            }
        } else {
            $file = new SplFileInfo('./_posts/'.$post, '_posts/', $post);
        }

        if (!isset($file) || $file === null) {
            return '';
        }
        $post = new Post($file, $context->get('site'));
        return $post->getUrl();
    }
}

<?php
/**
 * Highlight.php
 *
 * Created By: jonathan
 * Date: 23/09/2017
 * Time: 00:02
 */

namespace Stati\LiquidTag;

use Liquid\AbstractTag;
use Liquid\Context;
use Stati\Link\Generator;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Finder\Finder;

class PostUrl extends AbstractTag
{
    public function render(Context $context)
    {
        $post = trim($this->markup);
        var_dump($post);
        $pattern = '*'.$post.'.*';
        var_dump($pattern);
        if (!pathinfo($post, PATHINFO_EXTENSION)) {
            $finder = new Finder();
            $finder->depth(' <= 1')
                ->files()
                ->in('./_posts/')
                ->name($pattern)
                ;
            foreach ($finder as $f) {
                $file = $f;
            }

        } else {
            $file = new SplFileInfo('./_posts/'.$post, '_posts/', $post);
        }
        if (!$file) {
            return '';
        }
        var_dump($context->get('site'));
        $linkGenerator = new Generator($context->get('site'));
        //TODO pass config here somehow
        echo 'URL IS '.$linkGenerator->getUrlForFile($file, 'post');
        return $linkGenerator->getUrlForFile($file, 'post');
    }

}
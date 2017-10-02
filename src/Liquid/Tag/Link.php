<?php
/**
 * Highlight.php
 *
 * Created By: jonathan
 * Date: 23/09/2017
 * Time: 00:02
 */

namespace Stati\Liquid\Tag;

use Liquid\AbstractTag;
use Liquid\Context;
use Stati\Exception\FileNotFoundException;
use Stati\Link\Generator;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Finder\Finder;
use Stati\Entity\Page;

class Link extends AbstractTag
{
    public function render(Context $context)
    {
        $filePath = trim($this->markup);
        $parts = explode('/', $filePath);
        $fileName = array_pop($parts);
        $fileDir = implode('/', $parts);

        if (!pathinfo($fileName, PATHINFO_EXTENSION)) {
            $finder = new Finder();
            $finder
                ->files()
                ->in('./'.$fileDir)
                ->name($fileName)
                ;
            if ($finder->count() === 0) {
                throw new FileNotFoundException('Could not find the post to link to');
            }
            foreach ($finder as $f) {
                $file = $f;
            }
        } else {
            $file = new SplFileInfo('./'.$filePath, $fileDir.'/', $fileName);
        }

        if (!isset($file) || $file === null) {
            return '';
        }
        $post = new Page($file, $context->get('site'));
        return $post->getUrl();
    }
}

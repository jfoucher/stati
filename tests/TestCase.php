<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
 */

namespace Stati\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Stati\Site\Site;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use org\bovigo\vfs\vfsStreamDirectory;
use Symfony\Component\Finder\SplFileInfo;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var Site
     */
    protected $site;

    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    protected $root;

    public function setUp()
    {
        $this->site = new Site([
            'permalink' => '/:year/',
            'includes_dir' => './',
            'layouts_dir' => __DIR__ . '/fixtures/'
        ]);
        $this->root = vfsStream::setup('/');
        vfsStream::copyFromFileSystem(__DIR__ . '/fixtures/', $this->root);
    }

    /**
     * @param string $path
     * @param string $content
     * @return SplFileInfo
     */
    public function createFile($path, $content = null)
    {
        $file = new vfsStreamFile(basename($path));
        $file->setContent($content);
        $this->createPath(dirname($path))->addChild($file);
        return new SplFileInfo($file->url(), dirname($file->path()), $file->getName());
    }

    /**
     * @param string $path
     * @return SplFileInfo
     */
    public function getFile($path)
    {
        $file = $this->root->getChild($path);
        return new SplFileInfo($file->url(), dirname($file->path()), $file->getName());
    }


    /**
     * @param $path
     * @return vfsStreamDirectory
     */
    protected function createPath($path)
    {
        $parentDirectory = $this->root;
        $dirnames = array_filter(explode(DIRECTORY_SEPARATOR, $path));
        foreach ($dirnames as $dirname) {
            if ($dirname == '.') {
                continue;
            }
            $directory = new vfsStreamDirectory($dirname);
            $parentDirectory->addChild($directory);
            $parentDirectory = $directory;
        }
        return $parentDirectory;
    }
}

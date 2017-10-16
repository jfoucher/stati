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

namespace Stati\Tests;

use Stati\Site\Site;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use org\bovigo\vfs\vfsStreamDirectory;
use Symfony\Component\Finder\SplFileInfo;

abstract class TestCase extends \PHPUnit_Framework_TestCase
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
            'layouts_dir' => 'tests/fixtures'
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

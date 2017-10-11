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

use Liquid\LiquidException;
use Stati\Entity\Collection;
use Stati\Site\Site;
use Stati\Tests\TestCase;
use Stati\Entity\Doc;
use Symfony\Component\Finder\SplFileInfo;

class DocTest extends TestCase
{
    public function testGetSetContent()
    {
        $site = new Site([
            'permalink' => '/:slug.html',
            'includes_dir' => './',
        ]);
        $file = $this->getFile('2017-08-10-simple-post.markdown');
        $doc = new Doc($file, $site);

        $this->assertEquals('<p>c</p>', $doc->getContent());

        $doc->setContent('b');
        $this->assertEquals('b', $doc->getContent());
    }

    public function testIncorrectLiquidGivesEmptyContent()
    {
        $site = new Site([
            'permalink' => '/:slug.html',
            'includes_dir' => './',
        ]);
        $file = $this->getFile('incorrect.md');
        $doc = new Doc($file, $site);
        $this->assertEquals('', $doc->getContent());
    }

    public function testNoTitleReturnsNull()
    {
        $site = new Site([
            'permalink' => '/:slug.html',
            'includes_dir' => './',
        ]);
        $file = $this->getFile('empty.md');
        $doc = new Doc($file, $site);
        $this->assertEquals(null, $doc->getTitle());
    }

    public function testNoContentReturnsEmpty()
    {
        $site = new Site([
            'permalink' => '/:slug.html',
            'includes_dir' => './',
        ]);

        $file = $this->getFile('empty.md');
        $doc = new Doc($file, $site);
        $this->assertEquals('', $doc->getContent());
        $this->assertEquals(null, $doc->getOutputPath());
        $this->assertEquals(null, $doc->getTitle());
        $this->assertEquals(null, $doc->getDate());
        $this->assertEquals(null, $doc->getUrl());
    }

    public function testHtmlFile()
    {
        $site = new Site([
            'permalink' => '/:slug.html',
            'includes_dir' => './',
        ]);
        $file = $this->getFile('index.html');
        $doc = new Doc($file, $site);
        $this->assertEquals('<p>b</p>
', $doc->getContent());
        $this->assertEquals('a', $doc->getTitle());
        $doc->setTitle('b');
        $this->assertEquals('b', $doc->getTitle());
        $this->assertEquals(null, $doc->getDate());
        $this->assertEquals('/', $doc->getUrl());
        $this->assertEquals('/index.html', $doc->getOutputPath());
    }

    public function testCategoriesInUrl()
    {
        $site = new Site([
            'permalink' => '/:categories/:slug.html',
            'includes_dir' => './',
        ]);
        $file = $this->getFile('2017-08-10-simple-post.markdown');
        $doc = new Doc($file, $site);
        $this->assertEquals('/one/two/simple-post.html', $doc->getUrl());
    }

    public function testCategoriesInUrlNoFile()
    {
        $site = new Site([
            'permalink' => '/:categories/:slug/',
            'includes_dir' => './',
        ]);
        $file = $this->getFile('2017-08-10-simple-post.markdown');
        $doc = new Doc($file, $site);
        $this->assertEquals('/one/two/simple-post/', $doc->getUrl());
        $this->assertEquals('/one/two/simple-post/index.html', $doc->getOutputPath());
    }

    public function testConfigDefaults()
    {
        $site = new Site([
            'permalink' => '/:categories/:slug/',
            'includes_dir' => './',
            'defaults' => [[
                'scope' => [
                    'path' => '_posts'
                ],
                'values' => [
                    'description' => 'post desc',
                    'excerpt' => 'c'
                ]
            ]
            ]
        ]);

        $file = $this->createFile(
            './_posts/2017-08-10-simple-post.markdown',
            <<<EOL
---
title: a
excerpt: "b"
categories:
  - one
  - two
---

c

EOL
);
        $doc = new Doc($file, $site);

        $this->assertEquals('post desc', $doc->description);
        $this->assertEquals('b', $doc->excerpt);
        $this->assertEquals('a', $doc->getTitle());
    }

    public function testCustomPermalink()
    {
        $site = new Site([
            'permalink' => '/:year/:month/:day/',
            'includes_dir' => './',
            'defaults' => [[
                'scope' => [
                    'path' => 'post_test'
                ],
                'values' => [
                    'description' => 'post desc',
                    'excerpt' => 'c'
                ]
            ]
            ]
        ]);
        $file = $this->getFile('/2017-08-12-custom-permalink.markdown');
        $doc = new Doc($file, $site);

        $this->assertEquals('/one/two/custom-permalink/', $doc->getUrl());
    }

    public function testWrongGetterReturnNull()
    {
        $site = new Site([
            'permalink' => '/:categories/:slug.html',
            'includes_dir' => './',
        ]);
        $file = $this->getFile('2017-08-10-simple-post.markdown');
        $doc = new Doc($file, $site);
        $this->assertEquals(null, $doc->absent);
    }

    public function testGetter()
    {
        $site = new Site([
            'permalink' => '/:categories/:slug.html',
            'includes_dir' => './',
        ]);
        $file = $this->getFile('2017-08-10-simple-post.markdown');
        $doc = new Doc($file, $site);
        $this->assertEquals('a', $doc->title);
        $this->assertEquals('a', $doc->get('title'));
    }

    public function testSetSite()
    {
        $site = new Site([
            'permalink' => '/:categories/:slug.html',
            'includes_dir' => './',
            'title' => 'test'
        ]);
        $file = $this->getFile('2017-08-10-simple-post.markdown');
        $doc = new Doc($file);
        $doc->setSite($site);
        $this->assertEquals('test', $doc->getSite()->title);
    }

    public function testGetDocReturnSlug()
    {
        $site = new Site([
            'permalink' => '/:categories/:slug.html',
            'includes_dir' => './',
            'title' => 'test'
        ]);
        $file = $this->getFile('2017-08-10-simple-post.markdown');
        $doc = new Doc($file, $site);
        $this->assertEquals('simple-post', (string)$doc);
    }

    public function testGetSetOutput()
    {
        $site = new Site([
            'permalink' => '/:categories/:slug.html',
            'includes_dir' => './',
            'title' => 'test'
        ]);
        $file = $this->getFile('2017-08-10-simple-post.markdown');
        $doc = new Doc($file, $site);
        $doc->setOutput('<head></head><body>'.$doc->getContent().'</body>');
        $this->assertEquals('<head></head><body><p>c</p></body>', $doc->getOutput());
    }
}

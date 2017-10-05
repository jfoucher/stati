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

use Stati\Entity\Collection;
use Stati\Site\Site;
use Stati\Tests\TestCase;
use Stati\Entity\Post;
use Symfony\Component\Finder\SplFileInfo;
use org\bovigo\vfs\vfsStreamDirectory;

class PostTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->site = new Site([
            'permalink' => '/:year/:month/:day/:slug.html',
            'includes_dir' => './',
            'layouts_dir' => __DIR__ . '/fixtures/post_test'
        ]);
    }

    public function testSimplePost()
    {
        $file = $this->getFile('2017-08-10-simple-post.markdown');
        $post = new Post($file, $this->site);

        $this->assertEquals('a', $post->title);
        $this->assertEquals('b', $post->excerpt);
        $this->assertEquals('/2017/08/10/simple-post.html', $post->url);
        $this->assertEquals('simple-post', $post->slug);
        $this->assertEquals('2017-08-10T00:00:00+00:00', $post->date);

        $expectedContent = '<p>c</p>';
        $this->assertEquals($expectedContent, $post->content);
    }

    public function testPostWithNext()
    {
        $file = $this->getFile('2017-08-10-simple-post.markdown');
        $nextFile = $this->getFile('2017-08-11-next-post.markdown');
        $post = new Post($file, $this->site);
        $nextPost = new Post($nextFile, $this->site);

        $post->setNext($nextPost);

        $this->assertEquals('next', $post->next->title);
        $this->assertEquals('next post', $post->next->excerpt);
        $this->assertEquals('/2017/08/11/next-post.html', $post->next->url);
        $this->assertEquals('next-post', $post->next->slug);
        $this->assertEquals('2017-08-11T00:00:00+00:00', $post->next->date);

        $expectedContent = '<p>next content</p>';
        $this->assertEquals($expectedContent, $post->next->content);
    }

    public function testPostWithPrevious()
    {
        $file = $this->getFile('2017-08-10-simple-post.markdown');
        $prevFile = $this->getFile('2017-08-11-next-post.markdown');
        $post = new Post($file, $this->site);
        $prevPost = new Post($prevFile, $this->site);

        $post->setPrevious($prevPost);

        $this->assertEquals('next', $post->previous->title);
        $this->assertEquals('next post', $post->previous->excerpt);
        $this->assertEquals('/2017/08/11/next-post.html', $post->previous->url);
        $this->assertEquals('next-post', $post->previous->slug);
        $this->assertEquals('2017-08-11T00:00:00+00:00', $post->previous->date);

        $expectedContent = '<p>next content</p>';
        $this->assertEquals($expectedContent, $post->previous->content);
    }

    public function testPostWithCollection()
    {
        $collection = new Collection('posts');
        $file = $this->getFile('2017-08-10-simple-post.markdown');

        $post = new Post($file, $this->site);
        $post->setCollection($collection);

        $this->assertEquals('posts', $post->collection->getLabel());
    }

    public function testPostWithBadFrontMatterDateShouldReturnFileDate()
    {
        $file = $this->createFile('2016-08-10-wrong-date.markdown', <<<EOL
---
title: a
date: WRONG_DATE
---

b
EOL
);
        $post = new Post($file, $this->site);
        $this->assertEquals('2016-08-10T00:00:00+00:00', $post->date);
    }

    public function testPostWithBadDatesShouldReturnNull()
    {
        $file = $this->createFile('201628-wrong-date.markdown', <<<EOL
---
title: a
date: WRONG_DATE
---

b
EOL
);
        $post = new Post($file, $this->site);
        $this->assertEquals(null, $post->date);
    }

    public function testPostWithFrontMatterDateShouldReturnDate()
    {
        $file = $this->createFile('201628-wrong-date.markdown', <<<EOL
---
title: a
date: Thu Sep 17 01:09:13 +0200 2011
---

b
EOL
);
        $post = new Post($file, $this->site);
        $this->assertEquals('2011-09-22T01:09:13+02:00', $post->date);
    }
}

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

class PostTest extends TestCase
{
    public function testSimplePost()
    {
        $site = new Site([
            'permalink' => '/:year/:month/:day/:title.html',
            'url' => 'https://test.com',
            'baseurl' => '',
        ]);
        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/2017-08-10-simple-post.markdown', './', '2017-08-10-simple-post.markdown');
        $post = new Post($file, $site);

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
        $site = new Site([
            'permalink' => '/:year/:month/:day/:title.html',
            'url' => 'https://test.com',
            'baseurl' => '',
        ]);
        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/2017-08-10-simple-post.markdown', './', '2017-08-10-simple-post.markdown');
        $nextFile = new SplFileInfo(__DIR__ . '/../fixtures/post_test/2017-08-11-next-post.markdown', './', '2017-08-11-next-post.markdown');
        $post = new Post($file, $site);
        $nextPost = new Post($nextFile, $site);

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
        $site = new Site([
            'permalink' => '/:year/:month/:day/:title.html',
            'url' => 'https://test.com',
            'baseurl' => '',
        ]);
        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/2017-08-10-simple-post.markdown', './', '2017-08-10-simple-post.markdown');
        $prevFile = new SplFileInfo(__DIR__ . '/../fixtures/post_test/2017-08-11-next-post.markdown', './', '2017-08-11-next-post.markdown');
        $post = new Post($file, $site);
        $prevPost = new Post($prevFile, $site);

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
        $site = new Site([
            'permalink' => '/:year/:month/:day/:title.html',
            'url' => 'https://test.com',
            'baseurl' => '',
        ]);

        $collection = new Collection('posts');

        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/2017-08-10-simple-post.markdown', './', '2017-08-10-simple-post.markdown');

        $post = new Post($file, $site);
        $post->setCollection($collection);

        $this->assertEquals('posts', $post->collection->getLabel());
    }
}

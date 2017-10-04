<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
 */

namespace Stati\Tests\Renderer;

use Stati\Entity\Collection;
use Stati\Entity\Post;
use Stati\Renderer\CollectionRenderer;
use Stati\Site\Site;
use Stati\Tests\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class CollectionRendererTest extends TestCase
{
    /**
     * @var Site
     */
    private $site;

    public function setUp()
    {
        $this->site = new Site([
            'permalink' => '/:year/',
            'includes_dir' => './',
            'layouts_dir' => __DIR__ . '/../fixtures/post_test'
        ]);
    }

    public function testNewCollectionRendererReturnsRenderer()
    {
        $renderer = new CollectionRenderer($this->site);
        $this->assertEquals('Stati\Renderer\CollectionRenderer', get_class($renderer));
    }

    public function testRenderAllOne()
    {
        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/2017-08-10-simple-post.markdown', './', '2017-08-10-simple-post.markdown');
        $post = new Post($file, $this->site);

        $collection = new Collection('posts');
        $collection->addDoc($post);
        $this->site->setCollections([$collection]);
        $renderer = new CollectionRenderer($this->site);

        $collections = $renderer->renderAll();
        $this->assertEquals(count($collections), 1);
        $this->assertEquals(count($collection->getDocs()), 1);
        $this->assertEquals($collection->getDocs()[0]->title, 'a');
        $this->assertEquals($collection->getDocs()[0]->output, '<p>c</p>');
    }

    public function testRenderAllThree()
    {
        $collection = $this->collectionWithThreePosts('posts');
        $this->site->setCollections([$collection]);
        $renderer = new CollectionRenderer($this->site);

        $collections = $renderer->renderAll();
        $this->assertEquals(count($collections), 1);
        $this->assertEquals(get_class($collections['posts']), 'Stati\Entity\Collection');
        $this->assertEquals(count($collections['posts']->getDocs()), 3);
        $this->assertEquals($collections['posts']->getDocs()[0]->title, 'a');
        $this->assertEquals($collections['posts']->getDocs()[0]->output, '<p>c</p>');
    }

    public function testPostOrder()
    {
        $collection = $this->collectionWithThreePosts('posts');
        $this->site->setCollections([$collection]);
        $renderer = new CollectionRenderer($this->site);

        $collections = $renderer->renderAll();
        $this->assertEquals(count($collections['posts']->getDocs()), 3);
        $this->assertTrue($collections['posts']->getDocs()[0]->date <= $collections['posts']->getDocs()[1]->date);
        $this->assertTrue($collections['posts']->getDocs()[1]->date <= $collections['posts']->getDocs()[2]->date);
    }

    private function collectionWithThreePosts($label = 'posts')
    {
        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/2017-08-10-simple-post.markdown', './', '2017-08-10-simple-post.markdown');
        $post1 = new Post($file, $this->site);
        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/2017-08-11-next-post.markdown', './', '2017-08-11-next-post.markdown');
        $post2 = new Post($file, $this->site);
        $file = new SplFileInfo(__DIR__ . '/../fixtures/post_test/2017-08-12-custom-permalink.markdown', './', '2017-08-12-custom-permalink.markdown');
        $post3 = new Post($file, $this->site);

        $collection = new Collection($label);
        $collection->addDoc($post1);
        $collection->addDoc($post2);
        $collection->addDoc($post3);

        return $collection;
    }
}

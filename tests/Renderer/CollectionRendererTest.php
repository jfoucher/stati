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

namespace Stati\Tests\Renderer;

use Stati\Entity\Collection;
use Stati\Entity\Post;
use Stati\Renderer\CollectionRenderer;
use Stati\Site\Site;
use Stati\Tests\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class CollectionRendererTest extends TestCase
{
    public function testNewCollectionRendererReturnsRenderer()
    {
        $renderer = new CollectionRenderer($this->site);
        $this->assertEquals('Stati\Renderer\CollectionRenderer', get_class($renderer));
    }

    public function testRenderAllOne()
    {
        $file = $this->getFile('/2017-08-10-simple-post.markdown');
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
        $file = $this->getFile('2017-08-10-simple-post.markdown');
        $post1 = new Post($file, $this->site);
        $file = $this->getFile('2017-08-11-next-post.markdown');
        $post2 = new Post($file, $this->site);
        $file = $this->getFile('2017-08-12-custom-permalink.markdown');
        $post3 = new Post($file, $this->site);

        $collection = new Collection($label);
        $collection->addDoc($post1);
        $collection->addDoc($post2);
        $collection->addDoc($post3);

        return $collection;
    }
}

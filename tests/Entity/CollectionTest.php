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

class CollectionTest extends TestCase
{
    public function testConstructorSetLabel()
    {
        $collection = new Collection('posts');
        $this->assertEquals('posts', $collection->getLabel());
    }

    public function testSetLabel()
    {
        $collection = new Collection('posts');
        $collection->setLabel('col');
        $this->assertEquals('col', $collection->getLabel());
    }

    public function testSetConfig()
    {
        $collection = new Collection('posts', ['permalink' => '/:year.html']);
        $this->assertEquals(['permalink' => '/:year.html'], $collection->getConfig());
        $collection->setConfig(['permalink' => '/b.html']);
        $this->assertEquals(['permalink' => '/b.html'], $collection->getConfig());
    }

    public function testCollectionDocs()
    {
        $file = $this->getFile('2017-08-10-simple-post.markdown');
        $post = new Post($file, $this->site);
        $collection = new Collection('posts');
        $collection->addDoc($post);

        $this->assertEquals(1, count($collection->getDocs()));
        $this->assertEquals('a', $collection->getDocs()[0]->getTitle());
    }

    public function testSetDocs()
    {
        $file = $this->getFile('2017-08-10-simple-post.markdown');
        $post = new Post($file, $this->site);

        $collection = new Collection('posts');
        $collection->setDocs([$post]);

        $this->assertEquals(1, count($collection->getDocs()));
        $this->assertEquals('a', $collection->getDocs()[0]->getTitle());
    }
}

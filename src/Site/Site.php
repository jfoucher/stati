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

namespace Stati\Site;

use Liquid\Liquid;
use Stati\Entity\Collection;
use Stati\Entity\Doc;
use Stati\Event\ConsoleOutputEvent;
use Stati\Event\WillResetSiteEvent;
use Stati\Reader\CollectionReader;
use Stati\Reader\StaticFileReader;
use Stati\Reader\PageReader;
use Stati\Reader\DataReader;
use Stati\Renderer\CollectionRenderer;
use Stati\Renderer\PageRenderer;
use Stati\Writer\CollectionWriter;
use Stati\Writer\StaticFileWriter;
use Stati\Writer\PageWriter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Stati\Site\SiteEvents;
use Stati\Event\DidResetSiteEvent;
use Stati\Event\WillReadSiteEvent;
use Stati\Event\SiteEvent;

class Site
{
    /**
     * Config from _config.yml
     *
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $posts;

    /**
     * @var array
     */
    protected $pages;

    /**
     * @var array
     */
    protected $staticFiles;

    /**
     * @var array
     */
    protected $relatedPosts;

    /**
     * @var array
     */
    protected $collections = [];

    /**
     * @var array
     */
    protected $data = [];


    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var string Site generation time
     */
    protected $time;

    /**
     * @var string
     */
    protected $toString;

    public function __construct(array $config)
    {
        $this->config = new Config($config);

        $this->config["cache_dir"] = sys_get_temp_dir() . '/' . $this->config['name'];

        if(!is_dir($this->config["cache_dir"])) {
            mkdir($this->config["cache_dir"], 0777, true);
        }


        $this->config['collections'] = array_merge($this->config['collections'], [
            'posts' => [
                'permalink' => $this->config['permalink'],
                'output' => true,
            ]
        ]);

        $this->dispatcher = new EventDispatcher();
    }

    public function process()
    {
        $this->dispatcher->dispatch(SiteEvents::WILL_PROCESS_SITE, new SiteEvent($this));

        $this->reset();

        $this->read();

        $this->generate();

        $this->render();

        $this->write();

        $this->dispatcher->dispatch(SiteEvents::DID_PROCESS_SITE, new SiteEvent($this));
    }

    public function reset()
    {
        $fs = new Filesystem();
        $this->dispatcher->dispatch(SiteEvents::WILL_RESET_SITE, new WillResetSiteEvent($this, $fs));
        $fs->remove($this->getDestination());
        $fs->mkdir($this->getDestination());
        $this->dispatcher->dispatch(SiteEvents::DID_RESET_SITE, new DidResetSiteEvent($this, $fs));
    }

    public function read()
    {
        //Read static files
        $this->dispatcher->dispatch(SiteEvents::WILL_READ_SITE, new WillReadSiteEvent($this));
        $this->dispatcher->dispatch(SiteEvents::WILL_READ_STATIC_FILES, new SiteEvent($this));
        $staticFileReader = new StaticFileReader($this);
        $this->setStaticFiles($staticFileReader->read());
        $this->dispatcher->dispatch(SiteEvents::DID_READ_STATIC_FILES, new SiteEvent($this));

        $this->dispatcher->dispatch(SiteEvents::WILL_READ_COLLECTIONS, new SiteEvent($this));
        //Read collections
        $collectionReader = new CollectionReader($this);
        $this->setCollections($collectionReader->read());
        $this->dispatcher->dispatch(SiteEvents::DID_READ_COLLECTIONS, new SiteEvent($this));

        $this->dispatcher->dispatch(SiteEvents::WILL_READ_PAGES, new SiteEvent($this));
        //Read pages
        $pageReader = new PageReader($this);
        $this->setPages($pageReader->read());
        $this->dispatcher->dispatch(SiteEvents::DID_READ_PAGES, new SiteEvent($this));

        $this->dispatcher->dispatch(SiteEvents::WILL_READ_DATA, new SiteEvent($this));
        //Read pages
        $dataReader = new DataReader($this);
        $this->setData($dataReader->read());
        $this->dispatcher->dispatch(SiteEvents::DID_READ_DATA, new SiteEvent($this));
        $this->dispatcher->dispatch(SiteEvents::DID_READ_SITE, new SiteEvent($this));
    }

    public function generate()
    {
        $this->dispatcher->dispatch(SiteEvents::WILL_GENERATE_SITE, new SiteEvent($this));
        $this->dispatcher->dispatch(SiteEvents::DID_GENERATE_SITE, new SiteEvent($this));
    }

    public function render()
    {
        $this->dispatcher->dispatch(SiteEvents::WILL_RENDER_SITE, new SiteEvent($this));
        $this->dispatcher->dispatch(SiteEvents::WILL_RENDER_COLLECTIONS, new SiteEvent($this));

        //Render Collections
        $collectionRenderer = new CollectionRenderer($this);
        $this->setCollections($collectionRenderer->renderAll());
        $this->dispatcher->dispatch(SiteEvents::DID_RENDER_COLLECTIONS, new SiteEvent($this));

        //Render Pages
        $this->dispatcher->dispatch(SiteEvents::WILL_RENDER_PAGES, new SiteEvent($this));
        $pageRenderer = new PageRenderer($this);
        $this->setPages($pageRenderer->renderAll());
        $this->dispatcher->dispatch(SiteEvents::DID_RENDER_PAGES, new SiteEvent($this));

        $this->dispatcher->dispatch(SiteEvents::DID_RENDER_SITE, new SiteEvent($this));
    }

    public function write()
    {
        $this->dispatcher->dispatch(SiteEvents::WILL_WRITE_SITE, new SiteEvent($this));
        // Write static files
        $this->dispatcher->dispatch(SiteEvents::WILL_WRITE_STATIC_FILES, new SiteEvent($this));

        $staticFileWriter = new StaticFileWriter($this);
        $staticFileWriter->writeAll();
        $this->dispatcher->dispatch(SiteEvents::DID_WRITE_STATIC_FILES, new SiteEvent($this));

        // Write Collection files
        $this->dispatcher->dispatch(SiteEvents::WILL_WRITE_COLLECTIONS, new SiteEvent($this));
        $collectionWriter = new CollectionWriter($this);
        $collectionWriter->writeAll();
        $this->dispatcher->dispatch(SiteEvents::DID_WRITE_COLLECTIONS, new SiteEvent($this));

        // Write Page files
        $this->dispatcher->dispatch(SiteEvents::WILL_WRITE_PAGES, new SiteEvent($this));
        $pageWriter = new PageWriter($this);
        $pageWriter->writeAll();
        $this->dispatcher->dispatch(SiteEvents::DID_WRITE_PAGES, new SiteEvent($this));
        $this->dispatcher->dispatch(SiteEvents::DID_WRITE_SITE, new SiteEvent($this));
    }

    /**
     * @param $item
     * @return mixed
     */
    public function __get($item = null)
    {
        if (!$item) {
            return null;
        }
        $getter = implode('', array_map(function ($part) {
            return ucfirst($part);
        }, explode('_', $item)));
        $field = lcfirst($getter);

        //If the method exists, return it
        if (method_exists($this, 'get'.$getter)) {
            return $this->{'get'.$getter}();
        }

        //If the property exists, return it
        if (property_exists(self::class, $field)) {
            return $this->{$field};
        }

        // If it's a collection, return the corresponding one
        if (isset($this->collections[$item])) {
            return $this->collections[$item]->getDocs();
        }

        // If this is a data item, return it;
        if (isset($this->data[$item])) {
            return $this->data[$item];
        }

        // If this is a config item, return it;
        if (isset($this->config[$item])) {
            return $this->config[$item];
        }

        return null;
    }

    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }

    public function get($item)
    {
        return $this->__get($item);
    }

    public function field_exists($item)
    {
        return true;
    }

    public function getTime()
    {
        if ($this->time) {
            return $this->time;
        }
        $this->time = date_create()->format(DATE_RFC3339);
        return $this->time;
    }

    public function getDestination()
    {
        if (isset($this->config['destination'])) {
            return $this->config['destination'];
        }
        return './_site/';
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getPosts()
    {
        if (isset($this->collections['posts'])) {
            return $this->collections['posts']->getDocs();
        }
        $col = new Collection('posts');
        return $col->getDocs();
    }

    /**
     * @param Collection $posts
     */
    public function setPosts($posts)
    {
        $this->posts = $posts;
    }

    /**
     * @return array
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param array $pages
     */
    public function setPages($pages)
    {
        $this->pages = $pages;
    }

    /**
     * @param Doc $page
     */
    public function addPage($page)
    {
        $this->pages[] = $page;
    }

    /**
     * @return array
     */
    public function getStaticFiles()
    {
        return $this->staticFiles;
    }

    /**
     * @param array $staticFiles
     */
    public function setStaticFiles($staticFiles)
    {
        $this->staticFiles = $staticFiles;
    }

    /**
     * @return array
     */
    public function getRelatedPosts()
    {
        return $this->relatedPosts;
    }

    /**
     * @param array $relatedPosts
     */
    public function setRelatedPosts($relatedPosts)
    {
        $this->relatedPosts = $relatedPosts;
    }

    /**
     * @return array
     */
    public function getCollections()
    {
        return $this->collections;
    }

    /**
     * @param array $collections
     */
    public function setCollections($collections)
    {
        $this->collections = $collections;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function setDispatcher($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function __toString()
    {
        if ($this->toString) {
            return $this->toString;
        }

        $str = implode('', self::arrayFlattenToString($this->config));
        $str .= implode('', self::arrayFlattenToString($this->data));
        $cols = array_map(function ($col) {
            /**
             * @var Collection $col
             */
            return implode('', array_map(function ($doc) {
                /**
                 * @var Doc $doc
                 */
                return $doc->getCacheFilename();
            }, $col->getDocs()));
        }, array_values($this->collections));

        $str .= implode('', $cols);
        $this->toString = $str;
        return $str;
    }

    public static function arrayFlattenToString($array)
    {
        $return = array();
        if (is_array($array)) {
            foreach ($array as $element) {
                if (is_array($element)) {
                    $return = array_merge($return, self::arrayFlattenToString($element));
                } elseif (is_object($element)) {
                    $return[] = (string)$element;
                } else {
                    $return[] = $element;
                }
            }
        }

        return $return;
    }
}

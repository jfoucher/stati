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

namespace Stati\Entity;

class Collection
{
    /**
     * The collection name
     * @var string
     */
    protected $label;
    /**
     * The collection documents (posts for example)
     * @var array
     */
    protected $docs;

    /**
     * The collection config (permalink, output, etc)
     * @var array
     */
    protected $config;

    public function __construct($label, $config = [])
    {
        $this->label = $label;
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getDocs()
    {
        return $this->docs;
    }

    /**
     * @param array $docs
     */
    public function setDocs($docs)
    {
        $this->docs = $docs;
    }

    /**
     * @param mixed $doc
     */
    public function addDoc($doc)
    {
        $this->docs[] = $doc;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param string
     * @param mixed $item
     * @return mixed
     */
    public function getConfigItem($item)
    {
        if (isset($this->config[$item])) {
            return $this->config[$item];
        };
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function __get($item)
    {
        if ($this->{$item}) {
            return $this->{$item};
        }
        if ($this->getConfigItem($item)) {
            return $this->getConfigItem($item);
        }
        return null;
    }

    public function __toString()
    {
        return $this->getLabel();
    }
}

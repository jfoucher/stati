<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
    }
}

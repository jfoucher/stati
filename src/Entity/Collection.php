<?php
/**
 * Collection.php
 *
 * Created By: jonathan
 * Date: 26/09/2017
 * Time: 22:37
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
}

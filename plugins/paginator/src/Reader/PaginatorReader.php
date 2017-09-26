<?php
/**
 * PaginatorReader.php
 *
 * Created By: jonathan
 * Date: 27/09/2017
 * Time: 01:14
 */

namespace Stati\Plugin\Paginator\Reader;

use Stati\Plugin\Paginator\Entity\Paginator;
use Stati\Reader\Reader;
use Stati\Site\Site;

class PaginatorReader extends Reader
{
    /**
     * @var Site
     */
    protected $site;

    public function __construct(array $config = [], Site $site)
    {
        parent::__construct($config);
        $this->site = $site;
    }

    public function read()
    {
        if (!isset($this->config['paginate'])) {
            return null;
        }
        $paginator = new Paginator($this->site->getPosts()->getDocs(), $this->config);
        return $paginator;
    }
}
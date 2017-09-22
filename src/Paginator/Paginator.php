<?php
/**
 * Paginator.php
 *
 * Created By: jonathan
 * Date: 22/09/2017
 * Time: 21:46
 */

namespace Stati\Paginator;

/**
 * Class Paginator
 * @package Stati\Paginator
 */
class Paginator
{
    private $posts;

    /**
     * Paginator constructor.
     * @param array $posts
     */
    public function __construct(array $posts)
    {
        $this->posts = $posts;
    }

    public function getPosts()
    {
        return $this->posts;
    }
}
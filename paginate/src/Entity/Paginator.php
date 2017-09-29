<?php
/**
 * Paginator.php
 *
 * Created By: jonathan
 * Date: 22/09/2017
 * Time: 21:46
 */

namespace Stati\Plugin\Paginate\Entity;

use Stati\Entity\Post;

/**
 * Class Paginator
 * @package Stati\Plugin\Paginator
 */
class Paginator
{
    /**
     * An array of all posts in the paginator
     *
     * @var array
     */
    protected $posts;

    /**
     * The site config
     *
     * @var array
     */
    protected $config;

    /**
     * The current page
     *
     * @var int
     */
    protected $page = 1;

    /**
     * How many posts per page
     *
     * @var array
     */
    public $per_page;

    /**
     * Paginator constructor.
     * @param array $posts
     * @param array $config
     */
    public function __construct(array $posts, array $config)
    {
        $this->posts = $posts;
        $this->config = $config;
        $this->per_page = $config['paginate'];
    }

    public function getPosts()
    {
        $start = isset($this->per_page) ? ($this->page - 1) * $this->per_page : 0;
        $length = isset($this->per_page) ? $this->per_page : count($this->posts);
        return array_slice($this->posts, $start, $length);
    }

    public function setPosts(array $posts)
    {
        $this->posts = $posts;
        return $this;
    }

    public function getAllPosts()
    {
        return $this->posts;
    }

    public function addPost(Post $post)
    {
        $this->posts[] = $post;
        return $this;
    }

    public function __get($item)
    {
        if (method_exists($this, 'get'.ucfirst($item))) {
            return $this->{'get'.ucfirst($item)}();
        }

        if ($item === 'total_posts') {
            return count($this->posts);
        }

        if ($item === 'total_pages') {
            return ceil(count($this->posts) / $this->per_page);
        }

        if ($item === 'previous_page') {
            if ($this->page > 1) {
                return $this->page - 1;
            }
            return null;
        }

        if ($item === 'paginate_path') {
            return $this->config['paginate_path'];
        }

        if ($item === 'previous_page_path') {
            if ($this->previous_page === null) {
                return null;
            }
            return str_replace('//', '/', '/'.str_replace(':num', $this->previous_page, $this->config['paginate_path']));
        }

        if ($item === 'next_page') {
            if ($this->page < ceil(count($this->posts) / $this->per_page)) {
                return $this->page + 1;
            }
        }

        if ($item === 'next_page_path') {
            if ($this->next_page === null) {
                return null;
            }
            return str_replace('//', '/', '/'.str_replace(':num', $this->next_page, $this->config['paginate_path']));
        }

        if ($item === 'current_page_path') {
            if ($this->page === null) {
                return null;
            }
            if ($this->page === 1) {
                return dirname(str_replace('//', '/', '/'.str_replace(':num', $this->page, $this->config['paginate_path'].'/'))).'/';
            }
            return str_replace('//', '/', '/'.str_replace(':num', $this->page, $this->config['paginate_path']));
        }

        return null;
    }
//
//    public function __toString()
//    {
//        return (string)count($this->posts);
//    }

    public function get($item)
    {
        return $this->__get($item);
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $page
     * @return Paginator
     */
    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }
}

<?php

/*
 * This file is part of the Stati package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Stati
 */

namespace Stati\Site;

class Config implements \ArrayAccess
{
    protected $values = [
        "name" => "default",
        "source" => ".",
        "destination" => "./_site",
        "collections_dir" => ".",
        "plugins_dir" => "_plugins",
        "layouts_dir" => "_layouts",
        "data_dir" => "_data",
        "includes_dir" => "_includes",
        "collections" => [],
        "output" => "true",


        # Handling Reading,
        "safe" => "false",
        "include" => [".htaccess"],
        "exclude" => ["Gemfile", "Gemfile.lock", "node_modules", "vendor/bundle/", "vendor/cache/", "vendor/gems/", "vendor/ruby/"],
        "keep_files" => [".git", ".svn"],
        "encoding" => "utf-8",
        "markdown_ext" => "markdown,mkdown,mkdn,mkd,md",
        "strict_front_matter" => false,

        # Filtering Content,
        "show_drafts" => "null",
        "limit_posts" => "0",
        "future" => false,
        "unpublished" => false,

        # Plugins,
        "whitelist" => [],
        "plugins" => [],

        # Conversion,
        "markdown" => "kramdown",
        "highlighter" => "rouge",
        "lsi" => false,
        "excerpt_separator" => "\n\n",
        "incremental" => false,

        # Serving,
        "detach" => "false",
        "port" => "4000",
        "host" => "127.0.0.1",
        "baseurl" => "", // does not include hostname,
        "show_dir_listing" => "false",

        # Outputting,
        "permalink" => "date",
        "paginate_path" => "/page:num",
        "timezone" => "null",
        "quiet" => "false",
        "verbose" => "false",
    ];

    public function __construct($config)
    {
        if (isset($config['layouts'])) {
            $config['layouts_dir'] = $config['layouts'];
            unset($config['layouts']);
        }

        $this->values = array_merge($this->values, $config);
        if (strpos('./', $this->values['layouts_dir']) === 0) {
            $this->values['layouts_dir'] = substr($this->values['layouts_dir'], 2);
        }

        if ($this->values['permalink'] === 'date') {
            $this->values['permalink'] = '/:year/:month/:day/';
        }
    }

    public function offsetGet($offset)
    {
        if (isset($this->values[$offset])) {
            return $this->values[$offset];
        }
        return null;
    }

    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    public function offsetSet($offset, $value)
    {
        $this->values[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
    }
}

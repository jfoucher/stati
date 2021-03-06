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

class SiteEvents
{
    /**
     * This event is triggered just before the site generation process start
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const WILL_PROCESS_SITE = 'site.will_process';

    /**
     * This event is triggered just before the site reset starts
     *
     * @Event("Stati\Event\WillResetSiteEvent")
     */
    const WILL_RESET_SITE = 'site.will_reset';

    /**
     * This event is triggered when the site reset is complete
     *
     * @Event("Stati\Event\DidResetSiteEvent")
     */
    const DID_RESET_SITE = 'site.did_reset';

    /**
     * This event is triggered just before we start reading all the files
     *
     * @Event("Stati\Event\WillReadSiteEvent")
     */
    const WILL_READ_SITE = 'site.will_read';

    /**
     * This event is triggered just before we start reading static files
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const WILL_READ_STATIC_FILES = 'site.will_read_static_files';

    /**
     * This event is triggered when we are done reading static files
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const DID_READ_STATIC_FILES = 'site.did_read_static_files';

    /**
     * This event is triggered just before we start reading collections (posts and user-defined collections)
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const WILL_READ_COLLECTIONS = 'site.will_read_collections';

    /**
     * This event is triggered when we are done reading all collections (including posts)
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const DID_READ_COLLECTIONS = 'site.did_read_collections';

    /**
     * This event is triggered just before we start reading pages (files that have frontmatter but are not part of a collection)
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const WILL_READ_PAGES = 'site.will_read_pages';

    /**
     * This event is triggered when we are done reading pages (files that have frontmatter but are not part of a collection)
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const DID_READ_PAGES = 'site.did_read_pages';

    /**
     * This event is triggered just before we start reading data (yml files in _data)
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const WILL_READ_DATA = 'site.will_read_data';

    /**
     * This event is triggered when we are done reading data
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const DID_READ_DATA = 'site.did_read_data';

    /**
     * This event is triggered when we are done reading files for the whole site
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const DID_READ_SITE = 'site.did_read';

    /**
     * This event is triggered just before we start generating the site
     * This step does nothing for the time being
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const WILL_GENERATE_SITE = 'site.will_generate';

    /**
     * This event is triggered when we are done generating the site
     * This step does nothing for the time being
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const DID_GENERATE_SITE = 'site.did_generate';

    /**
     * This event is triggered just before we start rendering the site (converting markdown to html, liquid, etc..)
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const WILL_RENDER_SITE = 'site.will_render';

    /**
     * This event is triggered just before we start rendering collections
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const WILL_RENDER_COLLECTIONS = 'site.will_render_collections';

    /**
     * This event is triggered when we are done rendering collections
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const DID_RENDER_COLLECTIONS = 'site.did_render_collections';

    /**
     * This event is triggered just before we start rendering pages
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const WILL_RENDER_PAGES = 'site.will_render_pages';

    /**
     * This event is triggered when we are done rendering pages
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const DID_RENDER_PAGES = 'site.did_render_pages';

    /**
     * This event is triggered when we are done rendering the whole site
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const DID_RENDER_SITE = 'site.did_render';

    /**
     * This event is triggered just before we start writing the rendered site to disk
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const WILL_WRITE_SITE = 'site.will_write';

    /**
     * This event is triggered just before we start writing the static files to disk
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const WILL_WRITE_STATIC_FILES = 'site.will_write_static_files';

    /**
     * This event is triggered just after the static files have been written to disk
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const DID_WRITE_STATIC_FILES = 'site.did_write_static_files';

    /**
     * This event is triggered just before we start writing collections to disk
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const WILL_WRITE_COLLECTIONS = 'site.will_write_collections';

    /**
     * This event is triggered just after the collections have been written to disk
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const DID_WRITE_COLLECTIONS = 'site.did_write_collections';

    /**
     * This event is triggered just before we start writing pages to disk
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const WILL_WRITE_PAGES = 'site.will_write_pages';

    /**
     * This event is triggered just after the pages have been written to disk
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const DID_WRITE_PAGES = 'site.did_write_pages';

    /**
     * This event is triggered just after the whole site have been written to disk
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const DID_WRITE_SITE = 'site.did_write';

    /**
     * This event is triggered just after the whole site has been written to disk
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const DID_PROCESS_SITE = 'site.did_process';

    /**
     * This event is triggered when something wants to write to the console during the generation process
     *
     * @Event("Stati\Event\ConsoleOutputEvent")
     */
    const CONSOLE_OUTPUT = 'console.output';
}

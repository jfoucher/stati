<?php
/**
 * SiteEvents.php
 *
 * Created By: jonathan
 * Date: 27/09/2017
 * Time: 20:19
 */

namespace Stati\Site;


class SiteEvents
{
    const WILL_PROCESS_SITE = 'site.will_process';
    const DID_PROCESS_SITE = 'site.did_process';
    const WILL_RESET_SITE = 'site.will_reset';
    const DID_RESET_SITE = 'site.did_reset';
    const WILL_READ_SITE = 'site.will_read';
    const WILL_READ_STATIC_FILES = 'site.will_read_static_files';
    const DID_READ_STATIC_FILES = 'site.did_read_static_files';
    const WILL_READ_COLLECTIONS = 'site.will_read_collections';
    const DID_READ_COLLECTIONS = 'site.did_read_collections';
    const WILL_READ_PAGES = 'site.will_read_pages';
    const DID_READ_PAGES = 'site.did_read_pages';
    const DID_READ_SITE = 'site.did_read';
    const WILL_GENERATE_SITE = 'site.will_generate';
    const DID_GENERATE_SITE = 'site.did_generate';
    const WILL_RENDER_SITE = 'site.will_render';
    const WILL_RENDER_STATIC_FILES = 'site.will_render_static_files';
    const DID_RENDER_STATIC_FILES = 'site.did_render_static_files';
    const WILL_RENDER_COLLECTIONS = 'site.will_render_collections';
    const DID_RENDER_COLLECTIONS = 'site.did_render_collections';
    const WILL_RENDER_PAGES = 'site.will_render_pages';
    const DID_RENDER_PAGES = 'site.did_render_pages';
    const DID_RENDER_SITE = 'site.did_render';
    const WILL_WRITE_SITE = 'site.will_write';
    const WILL_WRITE_STATIC_FILES = 'site.will_write_static_files';
    const DID_WRITE_STATIC_FILES = 'site.did_write_static_files';
    const WILL_WRITE_COLLECTIONS = 'site.will_write_collections';
    const DID_WRITE_COLLECTIONS = 'site.did_write_collections';
    const WILL_WRITE_PAGES = 'site.will_write_pages';
    const DID_WRITE_PAGES = 'site.did_write_pages';
    const DID_WRITE_SITE = 'site.did_write';
    const CONSOLE_OUTPUT = 'console.output';
}
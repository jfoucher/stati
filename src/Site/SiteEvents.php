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
    const DID_READ_SITE = 'site.did_read';
    const WILL_GENERATE_SITE = 'site.will_generate';
    const DID_GENERATE_SITE = 'site.did_generate';
    const WILL_RENDER_SITE = 'site.will_render';
    const DID_RENDER_SITE = 'site.did_render';
    const WILL_WRITE_SITE = 'site.will_write';
    const DID_WRITE_SITE = 'site.did_write';
}
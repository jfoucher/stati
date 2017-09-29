<?php
/**
 * SiteEvents.php
 *
 * Created By: jonathan
 * Date: 27/09/2017
 * Time: 20:19
 */

namespace Stati\Liquid;

class TemplateEvents
{

    /**
     * This event is triggered just before the template variables are set for the post/page content
     * It allows plugin to modify the variables passed to Liquid just before rendering post/page content
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const SETTING_TEMPLATE_VARS = 'template.setting_vars';

    /**
     * This event is triggered just before the template variables are set for the layouts
     * It allows plugin to modify the variables passed to Liquid when rendering the layouts containing posts/pages
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const SETTING_LAYOUT_TEMPLATE_VARS = 'template.setting_layout_vars';
}

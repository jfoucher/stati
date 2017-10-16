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

namespace Stati\Liquid;

class TemplateEvents
{

    /**
     * This event is triggered just before the template variables are set for the post/page content
     * It allows plugin to modify the variables passed to Liquid just before rendering post/page content
     *
     * @Event("Stati\Event\SettingTemplateVarsEvent")
     */
    const SETTING_TEMPLATE_VARS = 'template.setting_vars';

    /**
     * This event is triggered just before the template variables are set for the layouts
     * It allows plugin to modify the variables passed to Liquid when rendering the layouts containing posts/pages
     *
     * @Event("Stati\Event\SettingTemplateVarsEvent")
     */
    const SETTING_LAYOUT_TEMPLATE_VARS = 'template.setting_layout_vars';

    /**
     * This event is trigerred just before we parse the template
     *
     * @Event("Stati\Event\SiteEvent")
     */
    const WILL_PARSE_TEMPLATE = 'template.will_parse';
}

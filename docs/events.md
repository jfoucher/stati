# Plugin events

Here are all the events that plugins can subscribe to.

- **Stati\Site\SiteEvents::WILL_PROCESS_SITE**

  This event is triggered just before the site generation process start
  
  Event dispatched is _Stati\Event\SiteEvent_

- **Stati\Site\SiteEvents::WILL_RESET_SITE**

  This event is triggered just before the site reset starts
  
  Event dispatched is _Stati\Event\WillResetSiteEvent_
  
- **Stati\Site\SiteEvents::DID_RESET_SITE**

  This event is triggered when the site reset is complete
  
  Event dispatched is _Stati\Event\DidResetSiteEvent_

- **Stati\Site\SiteEvents::WILL_READ_SITE**

  This event is triggered just before we start reading all the files
  
  Event dispatched is _Stati\Event\WillReadSiteEvent_

- **Stati\Site\SiteEvents::WILL_READ_STATIC_FILES**

  This event is triggered just before we start reading static files
  
  Event dispatched is _Stati\Event\SiteEvent_

- **Stati\Site\SiteEvents::DID_READ_STATIC_FILES**

  This event is triggered when we are done reading static files
  
  Event dispatched is _Stati\Event\SiteEvent_

- **Stati\Site\SiteEvents::WILL_READ_COLLECTIONS**

  This event is triggered just before we start reading collections (posts and user-defined collections)
  
  Event dispatched is _Stati\Event\SiteEvent_

- **Stati\Site\SiteEvents::DID_READ_COLLECTIONS**

  This event is triggered when we are done reading all collections (including posts)
  
  Event dispatched is _Stati\Event\SiteEvent_

- **Stati\Site\SiteEvents::WILL_READ_PAGES**

  This event is triggered just before we start reading pages (files that have frontmatter but are not part of a collection)
  
  Event dispatched is _Stati\Event\SiteEvent_

- **Stati\Site\SiteEvents::DID_READ_PAGES**

  This event is triggered when we are done reading pages (files that have frontmatter but are not part of a collection)
  
  Event dispatched is _Stati\Event\SiteEvent_

- **Stati\Site\SiteEvents::DID_READ_SITE**

  This event is triggered when we are done reading files for the whole site
  
  Event dispatched is _Stati\Event\SiteEvent_
  
- **Stati\Site\SiteEvents::WILL_GENERATE_SITE**

  This event is triggered just before we start generating the site
  This step does nothing for the time being
  
  Event dispatched is _Stati\Event\SiteEvent_

- **Stati\Site\SiteEvents::DID_GENERATE_SITE**

  This event is triggered when we are done generating the site
  This step does nothing for the time being
  
  Event dispatched is _Stati\Event\SiteEvent_

- **Stati\Site\SiteEvents::WILL_RENDER_SITE**

  This event is triggered just before we start rendering the site (converting markdown to html, liquid, etc..)
  
  Event dispatched is _Stati\Event\SiteEvent_

- **Stati\Site\SiteEvents::WILL_RENDER_COLLECTIONS**

  This event is triggered just before we start rendering collections
  
  Event dispatched is _Stati\Event\SiteEvent_

- **Stati\Site\SiteEvents::DID_RENDER_COLLECTIONS**

  This event is triggered when we are done rendering collections
  
  Event dispatched is _Stati\Event\SiteEvent_

- **Stati\Site\SiteEvents::WILL_RENDER_PAGES**

  This event is triggered just before we start rendering pages
  
  Event dispatched is _Stati\Event\SiteEvent_

- **Stati\Site\SiteEvents::DID_RENDER_PAGES**

  This event is triggered when we are done rendering pages
  
  Event dispatched is _Stati\Event\SiteEvent_

- **Stati\Site\SiteEvents::DID_RENDER_SITE**

  This event is triggered when we are done rendering the whole site
  
  Event dispatched is _Stati\Event\SiteEvent_

- **Stati\Site\SiteEvents::WILL_WRITE_SITE**

  This event is triggered just before we start writing the rendered site to disk
  
  Event dispatched is _Stati\Event\SiteEvent_

- **Stati\Site\SiteEvents::WILL_WRITE_STATIC_FILES**

  This event is triggered just before we start writing the
  - **Static files to disk
  
  Event dispatched is _Stati\Event\SiteEvent_

- **Stati\Site\SiteEvents::DID_WRITE_STATIC_FILES**

  This event is triggered just after the static files have been written to disk
  
  Event dispatched is _Stati\Event\SiteEvent_

- **Stati\Site\SiteEvents::WILL_WRITE_COLLECTIONS**

  This event is triggered just before we start writing collections to disk
  
  Event dispatched is _Stati\Event\SiteEvent_
  
- **Stati\Site\SiteEvents::DID_WRITE_COLLECTIONS**

  This event is triggered just after the collections have been written to disk
  
  Event dispatched is _Stati\Event\SiteEvent_
  
- **Stati\Site\SiteEvents::WILL_WRITE_PAGES**

  This event is triggered just before we start writing pages to disk
  
  Event dispatched is _Stati\Event\SiteEvent_

- **Stati\Site\SiteEvents::DID_WRITE_PAGES**

  This event is triggered just after the pages have been written to disk
  
  Event dispatched is _Stati\Event\SiteEvent_
  
- **Stati\Site\SiteEvents::DID_WRITE_SITE**

  This event is triggered just after the whole site have been written to disk
  
  Event dispatched is _Stati\Event\SiteEvent_
  
- **Stati\Site\SiteEvents::DID_PROCESS_SITE**

  This event is triggered just after the whole site has been written to disk
  
  Event dispatched is _Stati\Event\SiteEvent_
  
- **Stati\Site\SiteEvents::CONSOLE_OUTPUT**

  This event is triggered when something wants to write to the console during the generation process
  
  Event dispatched is _Stati\Event\ConsoleOutputEvent_
    
- **Stati\LiquidTemplateEvents::SETTING_TEMPLATE_VARS**

  This event is triggered just before the template variables are set for the post/page content
  It allows plugin to modify the variables passed to Liquid just before rendering post/page content
  
  Event dispatched is _Stati\Event\SettingTemplateVarsEvent_

- **Stati\LiquidTemplateEvents::SETTING_LAYOUT_TEMPLATE_VARS**

  This event is triggered just before the template variables are set for the layouts
  It allows plugin to modify the variables passed to Liquid when rendering the layouts containing posts/pages
  
  Event dispatched is _Stati\Event\SettingTemplateVarsEvent_

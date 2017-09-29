

# Stati plugins

### Plugin structure

The plugins are based on composer and are automatically included by Stati at runtime.

Put your code in the `src/` folder. This folder should at a minimum contain a namespaced class that extends the `\Stati\Plugin\Plugin` class.

The namespaces for all classes in your plugin should start with `\Stati\Plugin\Yourplugin\` where `yourplugin` is the name of the folder your plugin resides in. So basically if your plugin is called `categories` it should have at least one file in the src folder called `Categories.php` containing a class definition as follows:

{% highlight php %}
    <?php
    namespace Stati\Plugin\Categories;
    use Stati\Plugin\Plugin;
    class Categories extends Plugin
    {
    }
{% endhighlight %}

The Plugin class itself is a [Symfony Event Subscriber](http://symfony.com/doc/current/components/event_dispatcher.html#using-event-subscribers), so your class should implement the `getSubscribedEvents` so that stati knows which events your plugin listens to. Here is an example from the related posts plugin:

{% highlight php %}
    <?php
    public static function getSubscribedEvents()
    {
        return array(
            TemplateEvents::SETTING_LAYOUT_TEMPLATE_VARS => 'onSettingTemplateVars',
            TemplateEvents::SETTING_TEMPLATE_VARS => 'onSettingTemplateVars',
        );
    }
{% endhighlight %}


# Stati

An extensible static site generator in PHP that can work with any existing jekyll site and get the same results.


## Install

### Option 1
The easiest way to install Stati is to simply run the following command:

{% highlight sh %}
curl -LSs https://stati.jfoucher.com/installer.php | php
{% endhighlight %}

This will download and install Stati to your current directory.

You can then move it (don't forget the `plugins` folder) to a folder that is in your `$PATH` such as `/usr/local/bin/

### Option 2

Alternatively, you can [download the latest release]({{ site.github.latest_release.assets[0].browser_download_url }}), extract it, and put it in your path (somewhere like `/usr/local/bin` for example)

## Running

Once the install is complete, change to your website directory and run `stati.phar generate` to generate your site, or run `stati.phar serve` to serve it locally.

## Requirements

If your highlight your code with `{% raw  %}{% highlight %} {% endhighlight %}{% endraw  %}` blocks, you must have `pygmentize` in your path, i.e. install pygments globally

If you use sass or scss, the `sass` or `scss` commands must be available.

## Plugins

The plugin architecture, based on the Symfony Event dispatcher, allows plugin authors to hook into various points in the site generation lifecycle to modify the output.

Please check the [plugin documentation](plugins.md)

## Contributing

Try Stati with your site and let me know what fails by opening an issue here.
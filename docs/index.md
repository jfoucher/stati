
# Stati

An extensible static site generator in PHP that can work with any existing jekyll site and get the same results.


### Install

Download stati.zip from the [latest release](https://github.com/jfoucher/stati/releases/latest), extract it, and put it in your path (somewhere like `/usr/local/bin` for example)

You can then change to your website directory and run `stati.phar generate` to generate your site, or run `stati.phar serve` to serve it locally.

### Requirements

If your highlight your code with `{% highlight %} {% endhighlight %}` blocks, you must have `pygmentize` in your path, i.e. install pygments globally

If you use sass or scss, the `sass` or `scss` commands must be available.

### Plugins

The plugin architecture, based on the Symfony Event dispatcher, allows plugin authors to hook into various points in the site generation lifecycle to modify the output.


### Contributing

Try Stati with your site and let me know what fails by opening an issue here.
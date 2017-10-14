---
title: Stati, the Jekyll-compatible, extensible, PHP static site generator
---

# Stati

An extensible static site generator in PHP that can work with any existing jekyll site and get the same results.


## Install

### Option 1

If you already have composer installed, the easiest way to install stati is to just run this command: 

{% highlight bash %}
composer global require stati/stati>={{ site.github.latest_release.tag_name }}
{% endhighlight %}

This will download and install Stati in the `~/.composer` folder. Make sure `~/.composer/vendor/bin/` is in your `$PATH` so that you can run stati from anywhere on your filesystem.

### Option 2

Alternatively, you can [download the latest release](https://raw.githubusercontent.com/jfoucher/stati/{{ site.github.latest_release.tag_name }}/build/stati)

Make the file executable with `chmod a+x ./stati` (on Linux and MacOS)

If you want, you can then move it somewhere in your path (something like `/usr/local/bin` for example).

You can try copy/pasting the command below to do all of this in one easy step:

{% highlight bash %}
curl -o stati https://raw.githubusercontent.com/jfoucher/stati/{{ site.github.latest_release.tag_name }}/build/stati && chmod a+x stati && mv stati /usr/local/bin/stati
{% endhighlight %}


## Running

Once the install is complete, change to your website directory and run `stati generate` to generate your site, or `stati serve` to serve it locally.

### Options

Both the `generate` and the `serve` command have the following options available:

- `--watch` shortcut `-w`
  This makes Stati watch your filesystem for changes. If it detects that one of your source files has changed, it will automatically regenerate your site.
  
- `--no-cache` shortcut `-nc`
  Completely deletes the Stati cache before generating your site.
  
The serve command has this additional option:

- `--port` shortcut `-p`
  The port to use for serving your website. For example `stati serve --port=8080` will make your site available on `https://localhost:8080/`.

## Requirements

If your highlight your code with `{% raw  %}{% highlight %} {% endhighlight %}{% endraw  %}` blocks, you must have `pygmentize` in your path, i.e. install pygments globally

If you use sass or scss, the `sass` or `scss` commands must be available.

## Plugins

Stati uses plugins extensively, even for some core functionality. Required plugins are installed automatically by composer, but if you want to install a specific plugin, you just have to run `composer require stati/plugin-name` or `composer global require stati/plugin-name` depending on whether you installed Stati locally or globally.

Alternatively, just copy the .phar file in the _plugins/ directory of your website, just like for Jekyll.

## Contributing

Try Stati with your site and let me know what fails by [opening an issue on Github](https://github.com/jfoucher/stati/issues).

The plugin architecture, based on the Symfony Event dispatcher, allows plugin authors to hook into various points in the site generation lifecycle to modify the output.

Please check the [plugin documentation](plugins.md)

## Status

[![Build Status](https://travis-ci.org/jfoucher/stati.svg?branch=master)](https://travis-ci.org/jfoucher/stati)

I run Stati on themes I can find, mostly from [jekyllthemes.org](https://jekyllthemes.org) you can view the results on the [Report]({% link report.md %}) page
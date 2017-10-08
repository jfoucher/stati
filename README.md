
# Stati

A static site generator in PHP that can work with any existing jekyll site and get the same results.

That is the end goal anyway.

### Install

For install and usage documentation, please see [the documentation](https://stati.jfoucher.com)

### Status

[![Build Status](https://travis-ci.org/jfoucher/stati.svg?branch=master)](https://travis-ci.org/jfoucher/stati)

- Generates all defined collections, including custom defined collections in _config.yml
- Paginates posts and saves paginated post to the correct places
- Plugin architecture based on Symfony events
- scss file conversion (you need `scss` to be in your `$PATH`)
- Code highlighting with pygments (you need `pygments` to be in your `$PATH`)

### Downsides

- No automatic regeneration when a file changes as of yet
- Not entirely completely compatible with jekyll, most notably with the lack of liquid filters (Work in progress)
- Slower than Jekyll on the first run, especially if you highlight your code with `{% highlight %}` blocks or have many scss/sass files.

### Contributing

- Try Stati with your site and let me know what fails by opening an issue here.
- Create or port some [plugins](https://stati.jfoucher.com/plugins.html)

### Why that name?

Because `static` turns out to be a reserved word in PHP. Who would've thought ?
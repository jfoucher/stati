
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

- Slow (about 5 times slower than jekyll on an empty cache, more or less the same once the cache is primed.)

  This is mostly due to using command line pygments for highlighting code blocks. If you don't use code highlighting, the slowdown will be much less noticeable
- No automatic regeneration when a file changes as of yet
- Not entirely completely compatible with jekyll, most notably with the lack of liquid filters (Work in progress)

### Contributing

Try Stati with your site and let me know what fails by opening an issue here.

### Why that name?

Because `static` turns out to be a reserved word in PHP. Who would've thought ?
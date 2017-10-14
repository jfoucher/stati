---
title: Theme building report
---

# Theme build report

Stati generated the following Jekyll themes from [JekyllThemes](http://jekyllthemes.org/) successfully 😁: 

{% for theme in site.data.success %}{% assign names = theme[0] | split:"_" %}{% if theme[1].result == true %}
- [{{ names[1] | capitalize }}](https://github.com/{{names[0]}}/{{names[1]}}){% endif %}{% endfor %}

But Stati failed to generate the following sites 😭: 
{% for theme in site.data.fail %}{% assign names = theme[0] | split:"_" %}
- [{{ names[1] | capitalize }}](https://github.com/{{names[0]}}/{{names[1]}})
  {% include theme_error.html %}{% endfor %}

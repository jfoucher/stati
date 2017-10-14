---
title: theme building report
---

# Theme build report

Stati generated the following Jekyll themes from [JekyllThemes](http://jekyllthemes.org/) successfully 😁: 

{% for theme in site.data.success %}{% assign names = theme[0] | split:"_" %}{% if theme[1].result == true %}
- [{{ names[1] | capitalize }}](https://github.com/{{names[0]}}/{{names[1]}}){% endif %}{% endfor %}

But Stati failed to generate the following sites 😭: 
{% for theme in site.data.success %}{% assign names = theme[0] | split:"_" %}{% if theme[1].result == false %}
- [{{ names[1] | capitalize }}](https://github.com/{{names[0]}}/{{names[1]}}){% include error err=theme[1].error %}{% endif %}{% endfor %}

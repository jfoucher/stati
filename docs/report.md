---
title: Theme building report
---

# Theme build report

{% assign total_themes = site.data.success.size | plus: site.data.fail.size %}

{% assign percentage = site.data.success.size | divided_by: total_themes | times: 100 | round: 2 %}

Stati successfully generated **{{site.data.success | size}}** themes out of **{{total_themes}}** total different themes, which is **{{ percentage }}%** success.

## Successes 😁

Stati generated the following Jekyll themes from [JekyllThemes](http://jekyllthemes.org/) successfully: 

{% for theme in site.data.success %}{% assign names = theme[0] | split:"_" %}{% if theme[1].result == true %}
- [{{ names[1] | capitalize }}](https://github.com/{{names[0]}}/{{names[1]}})

{% endif %}{% endfor %}

## Failures 😭

But Stati failed to generate the following sites:
{% for theme in site.data.fail %}{% assign names = theme[0] | split:"_" %}
- [{{ names[1] | capitalize }}](https://github.com/{{names[0]}}/{{names[1]}}) <a href="#" style="font-size: 0.8em" class="view-errors">View Errors</a>
  
  {% include theme_error.html %}
{% endfor %}

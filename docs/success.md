Stati generated the following Jekyll themes from [JekyllThemes](http://jekyllthemes.org/) successfully : 

{% for theme in site.success %}
{% if theme[1] == true %}
{% assign names = theme[0] | split "_" %}
{% assign theme_name = names[1] %}
- [{{ theme_name }}](https://github.com/{{ names[0] }}/{{ names[1] }})
{% endif %}

{% endfor %}

And we have errors for these : 

{% for theme in site.success %}
{% if theme[1] == false %}
{% assign names = theme[0] | split "_" %}
{% assign theme_name = names[1] %}
- [{{ theme_name }}](https://github.com/{{ names[0] }}/{{ names[1] }})
{% endif %}

{% endfor %}
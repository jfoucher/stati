Stati generated the following Jekyll themes from [JekyllThemes](http://jekyllthemes.org/) successfully : 

{% highlight json %}
{{ site.data.success | jsonify }}

{% endhighlight %}

{% for theme in site.data.success %}
{% highlight json %}
{{ theme | jsonify }}
{% endhighlight %}
{% if theme[1] == true %}
{% assign names = theme[0] | split "_" %}
{% assign theme_name = names[1] %}
- [{{ theme_name }}](https://github.com/{{ names[0] }}/{{ names[1] }})
{% endif %}

{% endfor %}

And we have errors for these : 

{% for theme in site.data.success %}
{% if theme[1] == false %}
{% assign names = theme[0] | split "_" %}
{% assign theme_name = names[1] %}
- [{{ theme_name }}](https://github.com/{{ names[0] }}/{{ names[1] }})
{% endif %}

{% endfor %}
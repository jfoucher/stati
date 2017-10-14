Stati generated the following Jekyll themes from [JekyllThemes](http://jekyllthemes.org/) successfully : 

{% highlight json %}
{{ site.data.success | jsonify }}
{% endhighlight %}

{% for theme in site.data.success %}

{% assign names = theme[0] | split:"_" %}
[{{ names[1] | capitalize }}](https://github.com/{{names[0]}}/{{names[1]}})


{% endfor %}

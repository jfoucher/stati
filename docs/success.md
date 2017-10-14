Stati generated the following Jekyll themes from [JekyllThemes](http://jekyllthemes.org/) successfully : 

{% highlight json %}
{{ site.data.success | jsonify }}
{% endhighlight %}

{% for theme in site.data.success %}

{% assign names = theme[0] | split:"_" %}
{{ theme[0] | capitalize }}
{% highlight json %}
{{ names | jsonify }}
{% endhighlight %}

{% endfor %}

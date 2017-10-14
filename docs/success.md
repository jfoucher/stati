Stati generated the following Jekyll themes from [JekyllThemes](http://jekyllthemes.org/) successfully : 

{% highlight json %}
{{ site.data.success | jsonify }}
{% endhighlight %}

{% for theme in site.data.success %}{% assign names = theme[0] | split:"_" %}{% if theme[1] == true %}
- [{{ names[1] | capitalize }}](https://github.com/{{names[0]}}/{{names[1]}}){% endif %}{% endfor %}

But Stati failed to generate the following sites : 
{% for theme in site.data.success %}
{% assign names = theme[0] | split:"_" %}
{% assign err = theme[0] | append:"_error"}
{% if theme[1] == false %}
- [{{ names[1] | capitalize }}](https://github.com/{{names[0]}}/{{names[1]}}) {{err}}

  {{ site.data.success[err] | join:"\n" }}
  
{% endif %}{% endfor %}
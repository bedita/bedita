<h2>Default template</h2>

{assign_associative var="options" object=$section.currentContent showForm=true}
{$view->element('show_comments', $options)}	

<pre>
{dump var=$section}
</pre>
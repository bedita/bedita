<h3>Language: {$currLang}</h3>
<h3>Frontend: [title] {$publication.title} - [id] {$publication.id} </h3>

{$view->element('form_search')}

<h3>Sections Tree</h3>
<pre>
{dump var=$sectionsTree}
</pre>
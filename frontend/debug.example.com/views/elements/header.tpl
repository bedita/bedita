<h3>current language: {$currLang} - other available: 
{foreach from=$conf->frontendLangs item="g" key="k"}
	{if $currLang != $k} <a title="{$g}" href="{$html->url('/')}lang/{$k}">{$k} - {$g}</a>
	{/if}
{/foreach}
</h3>
<hr/>

<h3>section <em>breadcrumb</em>:  </h3>
<a href="{$html->url('/')}" >{$publication.public_name|default:$publication.title}</a>&nbsp;&gt;&nbsp; 
{if (!empty($section.pathSection))}
	{foreach from=$section.pathSection item="sec"}
	<a href="{$html->url('/')}{$sec.nickname}" >{$sec.title}</a>&nbsp;&gt;&nbsp;
	{/foreach}
{/if}
{if ($section.id != $publication.id)}
	<a href="{$html->url($section.path)}" >{$section.title}</a>
{/if}
<br/>

{if !empty($section.currentContent)}
<hr/>
<h3>current content:</h3>
<a href="{$html->url($section.path)}/{$section.currentContent.nickname}" >{$section.currentContent.title}</a>
<br/>
<a href="javascript:void(0)" class="open-close-link">show/hide</a>
<div style="display: none">
<pre>
{dump var=$section.currentContent}
</pre>
</div>
{/if}

{if !empty($section.childSections)}
<hr/>
<h3>sections in this section: $section.childSections</h3>
<ul>
	{foreach from=$section.childSections item="subsection"}
		<li><a href="{$html->url('/')}{$subsection.nickname}">{$subsection.title}</a></li>
	{/foreach}
</ul>
{/if}

{if !empty($section.childContents)}
<hr/>
<h3>contents in this section: $section.childContents</h3>
<ul>
	{foreach from=$section.childContents item="object"}
		<li><a href="{$html->url('/')}{$section.nickname}/{$object.nickname}">{$object.title}</a></li>
	{/foreach}
</ul>
{/if}
		

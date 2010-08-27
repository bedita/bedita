<h3>{t}current language{/t}: {$currLang} - {t}other available{/t}: 
{foreach from=$conf->frontendLangs item="g" key="k"}
	{if $currLang != $k} <a title="{$g}" href="{$html->url('/')}lang/{$k}">{$k} - {$g}</a>
	{/if}
{/foreach}
</h3>
<hr/>

<h3>{t}section breadcrumb{/t}:  </h3>
{$beFront->breadcrumb()}
<br/>

{if !empty($section.currentContent)}
<hr/>
<h3>{t}current content{/t}: <a href="{$html->url($section.currentContent.canonicalPath)}" >{$section.currentContent.title}</a></h3>
<a href="javascript:void(0)" class="open-close-link">{t}show/hide{/t}</a>
<div style="display: none">
<pre>
{dump var=$section.currentContent}
</pre>
</div>
{/if}

{if !empty($section.childSections)}
<hr/>
<h3>{t}sections in this section{/t}: $section.childSections</h3>
<ul>
	{foreach from=$section.childSections item="subsection"}
		<li><a href="{$html->url($subsection.canonicalPath)}">{$subsection.title}</a></li>
	{/foreach}
</ul>
{/if}

{if !empty($section.childContents)}
<hr/>
<h3>{t}contents in this section{/t}: $section.childContents</h3>
<ul>
	{foreach from=$section.childContents item="object"}
		<li><a href="{$html->url($object.canonicalPath)}">{$object.title}</a></li>
	{/foreach}
</ul>
{/if}
		

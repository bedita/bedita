{if ($section.id != $publication.id) && ($section.nickname != 'footer-docs')}
<div class="breadcrumb">
	{$beFront->breadcrumb("&nbsp;&gt;&nbsp;", false, "subon")}
</div>
{/if}

<div class="subdocs">

	<ul>		
	{if !empty($section.childContents) && ($section.nickname != 'footer-docs')}
		{foreach from=$section.childContents item="object"}
		<li><a href="{$html->url($object.canonicalPath)}" 
		{if $section.currentContent.nickname == $object.nickname}class="subon"{/if}>{$object.title}</a></li>
		{/foreach}
	{/if}
		
	{if !empty($section.childSections) && ($section.id != $publication.id)}
		{foreach from=$section.childSections item="subsection"}
		<h1><a href="{$html->url($subsection.canonicalPath)}">{$subsection.title}</a></h1>
		{/foreach}
		{/if}
	</ul>

</div>


	
{strip}

{if isset($conf->staging) && ($conf->staging)}
{$view->element('staging_toolbar')}
{/if}

{$view->element('header')}

<div class="main">

	<div class="content-main">	

	{$view->element('menu')}

{if (!empty($section.currentContent))}

	{if $section.currentContent.object_type == "Gallery"}

		{$view->element('gallery')}
		
	{else}
	
		{if (!empty($section.currentContent.abstract)) or (isset($section.currentContent.relations))}
		
			{assign var="class" value="twocols"}
			
		{/if}
	
	
	<div class="textC {$class|default:''}">
		
		<h1>{$section.currentContent.title}</h1>

		[{t}created on{/t} {$section.currentContent.created|date_format:$conf->dateTimePattern} {t}by{/t} <i>{$section.currentContent.creator|default:$section.currentContent.UserCreated.realname}</i>]<br/>
		[{t}modified on{/t} {$section.currentContent.modified|date_format:$conf->dateTimePattern}]<br/>
		[{t}comments{/t}: {if ($section.currentContent.comments == 'off')}{t}off{/t}{else}{$section.currentContent.num_of_comment|default:0}{/if}]<br/>

		{if !empty($section.currentContent.Tag)}
		tags:  | {foreach from=$section.currentContent.Tag item="tag"}<a href="{$html->url('/tag/')}{$tag.url_label}">{$tag.label}</a> | {/foreach}<br/>
		{/if}
		{if !empty($section.currentContent.Category)}
		categories:  | {foreach from=$section.currentContent.Category item="cat"}{$cat.label} | {/foreach}<br/>
		{/if}
		
		<h3>{$section.currentContent.description}</h3>

		<p class="testo">{$section.currentContent.body}</p>

		<hr/>
		{assign_associative var="options" object=$section.currentContent showForm=true}
		{$view->element('show_comments', $options)}

	</div>
		
	<div class="abstract {$class|default:''}">

			{$section.currentContent.abstract|default:''}
			{$view->element('related')}
			
	</div>
		
	{/if}	
		
	</div>
	
{/if}
	
</div>	
		
{$view->element('footer')}

{/strip}
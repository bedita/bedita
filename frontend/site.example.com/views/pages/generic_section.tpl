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
		
		<div style="font-size: 0.8em; border-bottom: 4px double #dddddd;overflow: hidden;">
			<div style="float: right; text-align: right;">
			{if $section.currentContent.modified == $section.currentContent.created}
				<br/>
			{/if}
			{t}published on{/t} <b>{$section.currentContent.publication_date|date_format:$conf->datePattern}</b> {t}at{/t} <b>{$section.currentContent.publication_date|date_format:"%H:%m"}</b>
			<br/>
			{if $section.currentContent.modified != $section.currentContent.created}
			{t}modified on{/t} {$section.currentContent.modified|date_format:$conf->datePattern} {t}at{/t} {$section.currentContent.modified|date_format:"%H:%m"}
			{/if}
			</div>
		
			<div style="float: left">
			{t}byAuthor{/t} <span style="font-style: italic;">{$section.currentContent.creator|default:$section.currentContent.UserCreated.realname}</span>
			<br/>
			<b>{$section.currentContent.num_of_comment|default:0}</b> {t}comments{/t}
			</div>
		</div>
		
		{if !empty($section.currentContent.Tag) || !empty($section.currentContent.Category)}
			<div style="margin: 3px 0; font-size: 0.8em; border-bottom: 4px double #dddddd;overflow: hidden;">
			{if !empty($section.currentContent.Tag)}
				tags:&nbsp;
				{foreach from=$section.currentContent.Tag item="tag" name="fctag"}
				<a href="{$html->url('/tag/')}{$tag.url_label}">{$tag.label}</a>
				{if !$smarty.foreach.fctag.last}, {/if}
				{/foreach}<br/>
			{/if}
			{if !empty($section.currentContent.Category)}
				categories:&nbsp;
				{foreach from=$section.currentContent.Category item="cat" name="fccat"}
				{$cat.label}
				{if !$smarty.foreach.fccat.last}, {/if}
				{/foreach}
			{/if}
			</div>
		{/if}
		
		<h3 style="margin-top: 20px;">{$section.currentContent.description}</h3>

		<p class="testo">{$section.currentContent.body|default:""}</p>

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
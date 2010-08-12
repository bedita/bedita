{strip}

{$view->element('header')}

<div class="main">

	<div class="content-main">
		<div class="textC">
		<h1 style="margin-bottom:30px;">{t}Objects tagged by{/t} "{$tag.label}"</h1>
		{if !empty($tag.items)}
			<ul>
			{foreach from=$tag.items item="object"}
				<li><a href="{$html->url('/')}{$object.nickname}">{$object.title}</a></li>
			{/foreach}
			</ul>
		{/if}	
		</div>
	</div>
</div>	
		
{$view->element('footer')}

{/strip}
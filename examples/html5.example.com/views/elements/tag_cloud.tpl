{if !empty($listTags)}
	<h2>{t}Tag cloud{/t}</h2>

	{foreach from=$listTags item=tag}
		<a title="{$tag.weight}" class="tagCloud {$tag.class|default:""}" href="{$html->url('/tag/')}{$tag.name}">{$tag.label}</a>
	{/foreach}
{/if}
		

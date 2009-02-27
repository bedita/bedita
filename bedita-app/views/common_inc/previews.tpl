
{if !empty($previews)}
	<ul class="menuleft insidecol">
		<li>
			<a href="javascript:void(0)" onclick="$('#previews').slideToggle();">{t}Previews{/t}</a>
			<ul id="previews" style="display:none;">
			{foreach from=$previews item="preview"}
				{if ($preview.url != $purl|default:'')}
				<li><a href="{$preview.url}" target="_blank">{$preview.desc}</a></li>
				{assign var="purl" value=$preview.url}
				{/if}
			{/foreach}
			</ul>
		</li>
	</ul>
{/if}

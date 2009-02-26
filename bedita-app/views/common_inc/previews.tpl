
{if !empty($previews)}
	<ul class="menuleft insidecol">
		<li>
			<a href="javascript:void(0)" onclick="$('#previews').slideToggle();">{t}Previews{/t}</a>
			<ul id="previews" style="display:none;">
			{foreach from=$previews item="preview"}
				<li style="width:140px;"><a href="{$preview.url}" target="_blank">{$preview.desc}</a></li>
			{/foreach}
			</ul>
		</li>
	</ul>
{/if}

{*
<ul class="menuleft insidecol">
	<li>
		<a href="javascript:void(0)" onclick="$('#previews2').slideToggle();">{t}Preview{/t}</a>
		<ul id="previews2" style="display:none; padding:0px !important; margin:0px !important">
			{section name=t loop=$tree}
				<li style="padding:5px 0px 0px 0px; line-height:1.15em;">
					<a title="preview in {$tree[t].nickname}" href="/">{$tree[t].title}</a>
				</li>
			{/section}
		</ul>
	</li>
</ul>
*}
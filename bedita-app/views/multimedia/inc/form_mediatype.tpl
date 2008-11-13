
{assign var='cat' value=$object.Category|default:''}
<div class="tab"><h2>{t}Media type{/t}</h2></div>
<div id="mediatypes">
	
<ul class="inline">
	{foreach from=$conf->mediaTypes item="media_type"}
		<li class="ico_{$media_type} {if $cat==$media_type}on{/if}">
		{$media_type} <input type="radio" name="mediatype" value="{$media_type}" {if $cat==$media_type}checked="checked"{/if}/>
		</li>
	{/foreach}
</ul>

<br style="clear:both !important" />
	
</div>


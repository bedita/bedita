<h2 class="showHideBlockButton">{t}Tags{/t}</h2>
<div class="blockForm" id="properties">
<fieldset>

Inserisci tag separati da virgole <a href="{$html->url('/tags/listAllTags/?&modal=false')}" class="thickbox">click</a><br/>
{strip}
<textarea name="tags" id="tagsArea" rows="3" cols="50">
{if !empty($object.ObjectCategory)}
	{foreach from=$object.ObjectCategory item="tag" name="ft"}
		{$tag.label}{if !$smarty.foreach.ft.last},&nbsp;{/if}
	{/foreach}
{/if}
</textarea>
{/strip}
</fieldset>
</div>
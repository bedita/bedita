{*
Template incluso.
Menu a DX
*}

{if !empty($object.note)}
<script type="text/javascript">
{literal}
	$(document).ready( function (){
		$("#editornotes").prev(".tab").BEtabsopen();
	});
{/literal}
</script>
{/if}

<div class="quartacolonna">	
	
	<div class="tab"><h2>{t}Editors Notes{/t}</h2></div>
<!-- old notes -->
	<div id="editornotes" style="margin-top:-10px; padding:10px; background-color:white;">
	{strip}
		<label>{t}editor notes{/t}:</label>
		<textarea name="data[note]" class="autogrowarea editornotes">
		  {$object.note|default:''}
		</textarea>
	{/strip}
	</div>
<!-- end old notes -->
{bedev}
	<div id="editornotes" style="margin-top:-10px; padding:10px; background-color:white;">
	{*dump var=$object.EditorNote*}
	{strip}
	{if (!empty($object.EditorNote))}
		{section name=p loop=$object.EditorNote|@sortby:"created"}
		<table class="editorheader ultracondensed" style="width:100%">
		<tr>
			<td class="autor">{$object.EditorNote[p].user_created}</td>
			<td class="date">{$object.EditorNote[p].created}</td>
		</tr>
		</table>
		<p class="editornotes">{$object.EditorNote[p].description}</p>
		{/section}
	{/if}
		<table class="ultracondensed" style="width:100%">
		<tr>
			<td class="autor">you</td>
			<td class="date">now</td>
			<td><img src="{$html->webroot}img/iconNotes.gif" alt="notes" /></td>
		</tr>
		</table>
		<textarea name="data[note]" class="autogrowarea editornotes"></textarea>
		<input type="submit" style="margin-top:5px" value="{t}send{/t}" />
	
	{/strip}
	{include file="../common_inc/BEiconstest.tpl}	
	</div>
{/bedev}
</div>



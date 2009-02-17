

<div class="tab"><h2>{t}Properties{/t}</h2></div>

<fieldset id="properties">			
			
<table class="bordered">
		
	<tr>

		<th>{t}status{/t}:</th>
		<td colspan="4">
			{if $object.fixed}
				{t}This object is fixed - some data is readonly{/t}
				<input type="hidden" name="data[fixed]" value="1" />
				<input type="hidden" name="data[status]" value="{$object.status}" />
			{else}
				{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->status separator="&nbsp;"}
			{/if}
		</td>

	</tr>


	{if !(isset($publication)) || $publication}

	<tr>
		<td colspan="2">
			<label>{t}scheduled from{/t}:</label>&nbsp;
			
			
			<input size="10" type="text" style="vertical-align:middle"
			class="dateinput" name="data[start]" id="start" 
			value="{if !empty($object.start)}{$object.start|date_format:$conf->datePattern}{/if}" />
			&nbsp;
			
			<label>{t}to{/t}: </label>&nbsp;
			
			<input size="10" type="text" 
			class="dateinput" name="data[end]" id="end" 
			value="{if !empty($object.end)}{$object.end|date_format:$conf->datePattern}{/if}" />

		</td>
	</tr>

	{/if}

	<tr>
		<th>{t}author{/t}:</th>
		<td>
			<input type="text" name="data[creator]" value="{$object.creator}" />
		</td>
	</tr>


	<tr>
		<th>{t}main language{/t}:</th>
		<td>
		{assign var=object_lang value=$object.lang|default:$conf->defaultLang}
		<select name="data[lang]" id="main_lang">
			{foreach key=val item=label from=$conf->langOptions name=langfe}
			<option {if $val==$object_lang}selected="selected"{/if} value="{$val}">{$label}</option>
			{/foreach}
			{foreach key=val item=label from=$conf->langsIso name=langfe}
			<option {if $val==$object_lang}selected="selected"{/if} value="{$val}">{$label}</option>
			{/foreach}
		</select>
		</td>
	</tr>
	
	{if isset($comments)}
	<tr>
		<th>{t}comments{/t}:</th>
		<td>
			<input type="radio" name="data[comments]" value="off"{if empty($object.comments) || $object.comments=='off'} checked{/if}/>{t}No{/t} 
			<input type="radio" name="data[comments]" value="on"{if !empty($object.comments) && $object.comments=='on'} checked{/if}/>{t}Yes{/t}
			<input type="radio" name="data[comments]" value="moderated"{if !empty($object.comments) && $object.comments=='moderated'} checked{/if}/>{t}Moderated{/t}
			&nbsp;&nbsp;
			{if isset($moduleList.comments) && $moduleList.comments.status == "on"}
				{if !empty($object.num_of_comment)}
					<a href="{$html->url('/')}comments/index/comment_object_id:{$object.id}"><img style="vertical-align:middle" src="{$html->webroot}img/iconComments.gif" alt="comments" /> ({$object.num_of_comment}) {t}view{/t}</a>
				{/if}
			{/if}
		</td>
	</tr>
	{/if}
</table>
	
</fieldset>

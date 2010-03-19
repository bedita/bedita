{* title and description *}

<div class="tab"><h2>{t}Title{/t}</h2></div>

<fieldset id="title">

	<label>{t}title{/t}:</label>
	<br />
	<input type="text" name="data[title]" value="{$object.title|escape:'html'|escape:'quotes'}" id="titleBEObject" />
	<br />
	<label>{t}description{/t}:</label>
	<br />
	<textarea id="subtitle" style="margin-bottom:2px; height:30px" class="shortdesc autogrowarea" name="data[description]">{$object.description|default:''|escape:'html'}</textarea>
	
	{bedev}
	<label>{t}public url{/t}:</label> 
	{foreach from=$previews item="preview"}
		<li><a class="graced" href="#nicknameBEObject" onclick="$('#advancedproperties').show(); $('#nicknameBEObject').focus()">
			{$preview.url}/<b style="font-size:1.2em">{$object.nickname}</b>
		</a></li>
	{/foreach}
	{/bedev}
</fieldset>

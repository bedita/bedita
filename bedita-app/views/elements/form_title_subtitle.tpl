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

</fieldset>

{bedev}
<div class="tab"><h2>{t}Previews{/t}</h2></div>

<fieldset id="previewsTab">
	{foreach from=$previews item="pubs"}
		<label>{$pubs.title}</label>
		<ul>
		{foreach from=$pubs.object_url item="object_url"}
			<li><a class="graced" href="#nicknameBEObject" onclick="$('#advancedproperties').show(); $('#nicknameBEObject').focus()">
				{$object_url.public_url}
				</a>
			</li>
		{/foreach}
		</ul>
		<ul>
		{foreach from=$pubs.object_url item="object_url"}
			<li><a class="graced" href="#nicknameBEObject" onclick="$('#advancedproperties').show(); $('#nicknameBEObject').focus()">
				{$object_url.staging_url}
				</a>
			</li>
		{/foreach}
		</ul>
	{/foreach}
</fieldset>

{/bedev}
{* title and description *}

<div class="tab"><h2>{t}Title{/t}</h2></div>

<fieldset id="title">

	<label>{t}title{/t}:</label>
	<br />
	<input type="text" id="title" name="data[title]" value="{$object.title|escape:'html'|escape:'quotes'}" id="titleBEObject"/>
	<br />
	<label>{t}description{/t}:</label>
	<br />
	<textarea id="subtitle" style="height:30px" class="shortdesc autogrowarea" name="data[description]">{$object.description|default:''|escape:'html'}</textarea>
	


</fieldset>

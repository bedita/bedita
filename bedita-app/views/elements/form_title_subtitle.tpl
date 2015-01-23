{* title and description *}

{$view->element('texteditor')}

<div class="tab"><h2>{t}Title & Description{/t}</h2></div>

<fieldset id="title">

	<label>{t}title{/t}:</label>
	<br />
	<input type="text" name="data[title]" value="{$object.title|escape|escape:'quotes'}" id="titleBEObject" style="width:100%" />
	<br />
	<label>{t}description{/t}:</label>
	<br />
	<textarea style="width:100%; margin-bottom:2px; height:30px" class="mceSimple subtitle" name="data[description]">{$object.description|default:''|escape}</textarea>
	<label>{t}unique name{/t} ({t}url name{/t}):</label>
	<br />
	<input type="text" id="nicknameBEObject" name="data[nickname]" style="font-style:italic; width:100%" value="{$object.nickname|escape|escape:'quotes'}"/>
	

</fieldset>
{* title and description *}

<div class="tab"><h2>{t}Title{/t}</h2></div>

<fieldset id="title">

<table>
	<tr>
		<th>{t}Title{/t}</th>
		<td><input type="text" id="title" name="data[title]" value="{$object.title|escape:'html'|escape:'quotes'}" id="titleBEObject"/></td>
	</tr>
	<tr>
		<th>{t}Description{/t}</th>
		<td><textarea id="subtitle" style="height:30px" class="shortdesc autogrowarea" name="data[description]">{$object.description|default:''|escape:'html'}</textarea></td>
	</tr>
	
</table>


</fieldset>

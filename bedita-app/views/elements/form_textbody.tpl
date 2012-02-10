
{$view->element('texteditor')}

<div class="tab"><h2>{t}Text{/t}</h2></div>

<fieldset id="long_desc_langs_container">

{if (!empty($addshorttext)) or (!empty($object.abstract))}

		<label>{t}short text{/t}:</label>
		<textarea name="data[abstract]" style="height:200px" class="mce abstract">{$object.abstract|default:''}</textarea>
		
		<label for="body">{t}long text{/t}:</label>

{/if}		
		<textarea name="data[body]" style="height:{$height|default:200}px" class="mce body">{$object.body|default:''}</textarea>

</fieldset>

{$view->element('texteditor')}

<div class="tab"><h2>{t}Text{/t}</h2></div>

<fieldset id="long_desc_langs_container">

{if (!empty($addshorttext)) or (!empty($object.abstract))}

		<label>{t}short text{/t}:</label>
		<textarea name="data[abstract]" class="mce abstract">{$object.abstract|default:''}</textarea>
		<label for="body">{t}long text{/t}:</label>
{/if}	
		<div id="bodyDropTarget" class="dropTarget">
			<div class='dropSubTarget allowed' rel='placeref' data-attributes='{literal}{"class": "placeref"}{/literal}' data-options='{literal}{"type": "append","object": "img"}{/literal}'>
				<p>{t}Drop here as placeref{/t}</p>
				<p>{t}Drop here as placeref{/t}</p>
			</div>
			<div class='dropSubTarget allowed' rel='placeholder' data-attributes='{literal}{"class": "placeholder"}{/literal}' data-options='{literal}{"type": "append", "object": "img"}{/literal}'>
				<p>{t}Drop here as placeholder{/t}</p>
				<p>{t}Drop here as placeholder{/t}</p>
			</div>
			<div class='dropSubTarget allowed' rel='simplelink' data-attributes='{literal}{"class": "modalLink", "target": "modal"}{/literal}' data-options='{literal}{"type": "wrap","selection":"required", "object": "a"}{/literal}'>
				<p>{t}Drop here as an anchor{/t}</p>
				<p>{t}Drop here as an anchor{/t}</p>
			</div>
			<div class="dropSubTarget denied">
				<p>{t}Select something in the editor{/t}</p>
			</div>
		</div>

		<textarea name="data[body]" class="mce body">{$object.body|default:''}</textarea>
</fieldset>
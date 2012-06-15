
{$view->element('texteditor')}

<div class="tab"><h2>{t}Text{/t}</h2></div>

<fieldset id="long_desc_langs_container">

{if (!empty($addshorttext)) or (!empty($object.abstract))}

		<label>{t}short text{/t}:</label>
		<textarea name="data[abstract]" style="height:200px" class="mce abstract">{$object.abstract|default:''}</textarea>
		
		<label for="body">{t}long text{/t}:</label>

{/if}	
		<!-- per il drag&drop degli oggetti multimediali-->
		<div id="bodyDropTarget" class="dropTarget">
			<div class='dropSubTarget allowed' rel='placeref' data-options='{literal}{"class": "placeref","target":"modal"}{/literal}'>
				<p>Rilascia qui per inserire come placeref</p>
			</div>
			<div class='dropSubTarget allowed' rel='placeholder' data-options='{literal}{"class": "placeholder","target":"modal"}{/literal}'>
				<p>Rilascia qui per inserire come placeholder</p>
			</div>
			<div class="dropSubTarget denied">
				<p>Seleziona prima qualcosa nell'editor</p>
			</div>
		</div>

		<textarea name="data[body]" style="height:{$height|default:200}px" class="mce body">{$object.body|default:''}</textarea>

</fieldset>
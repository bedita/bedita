
{$view->element('texteditor')}

<div class="tab"><h2>{t}Text{/t}</h2></div>

<fieldset id="long_desc_langs_container">

{if (!empty($addshorttext)) or (!empty($object.abstract))}

		<label>{t}short text{/t}:</label>
		<textarea name="data[abstract]" style="height:200px" class="mce abstract">{$object.abstract|default:''}</textarea>
		
		<label for="body">{t}long text{/t}:</label>
		<script>
		  	CKEDITOR.replace( 'data[abstract]' );
		  	CKEDITOR.add
		</script>
{/if}	
		<!-- per il drag&drop degli oggetti multimediali-->
		<div id="bodyDropTarget" class="dropTarget">
			<div class='dropSubTarget allowed' rel='placeref' data-attributes='{literal}{"class": "placeholder placeref","target":"modal"}{/literal}' data-options='{literal}{"type": "append","object": "img"}{/literal}'>
				<p>Rilascia qui per inserire come placeref</p>
			</div>
			<div class='dropSubTarget allowed' rel='placeholder' data-attributes='{literal}{"class": "placeholder","target":"modal"}{/literal}' data-options='{literal}{"type": "append", "object": "img"}{/literal}'>
				<p>Rilascia qui per inserire come placeholder</p>
			</div>
			<div class='dropSubTarget allowed' rel='simplelink' data-attributes='{literal}{"class": "simplelink","target":"modal"}{/literal}' data-options='{literal}{"type": "wrap","selection":"required", "object": "a"}{/literal}'>
				<p>Rilascia qui per inserire come link semplice</p>
			</div>
			<div class="dropSubTarget denied">
				<p>Seleziona prima qualcosa nell'editor</p>
			</div>
		</div>

		<textarea name="data[body]" style="height:{$height|default:500}px" class="mce body">{$object.body|default:''}</textarea>
		<script>
		  	CKEDITOR.replace( 'data[body]' );
		  	CKEDITOR.add
		</script>
</fieldset>
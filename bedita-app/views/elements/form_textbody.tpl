
{$view->element('texteditor')}

<div class="tab"><h2 {if empty($object.body) && empty($object.abstract)}class="empty"{/if}>{t}Text{/t}</h2></div>

<fieldset id="long_desc_langs_container">

{$shortText = $conf->addShortText|default:false}

{if ($shortText) or (!empty($object.abstract))}

		<label>{t}short text{/t}:</label>
		<textarea name="data[abstract]" class="mce abstract">{$object.abstract|default:''|escape}</textarea>
		<label for="body">{t}long text{/t}:</label>
{/if}	
		<div id="bodyDropTarget" class="dropTarget">
			<div class='dropSubTarget allowed' rel='simplelink'>
				<p>{t}Drop here as an anchor{/t}</p>
				<p>{t}Drop here as an anchor{/t}</p>
			</div>
		</div>

		<textarea name="data[body]" class="mce body">{$object.body|default:''|escape}</textarea>
</fieldset>
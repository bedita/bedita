<div class="tab"><h2>{t}Text{/t}</h2></div>

<fieldset id="text">
			<label class="block" for="testo">{t}Short text{/t}:</label>

			<textarea name="data[abstract]" id="testo" style="font-size:13px; width:510px; height:150px;">{$object.abstract|default:''}</textarea>

			<label class="block" for="testoL">{t}Long text{/t}:</label>

			<textarea name="data[body]" id="testoL" style="font-size:13px; width:510px; height:150px;">{$object.body|default:''}</textarea>

</fieldset>


<fieldset>
{if isset($conf->fckeditor)}{$fck->load()}{/if}
<textarea id="Longdesc" name="data[longDesc]" rows="30" cols="30">{if ($object.longDesc)}{$object.longDesc}{/if}</textarea>
<textarea id="LongdescSolotext" name="data[longDesc]" rows="30" cols="30" style="display:none;height:300px;width:750px;" disabled="disabled">{if ($object.longDesc)}{$object.longDesc}{/if}</textarea>
{if isset($conf->fckeditor)}{$fck->editor("longdesc")}{/if}
{if isset($conf->fckeditor)}
<br/>
<input type="radio" name="editortype" id="fck" checked="checked"
	onclick="javascript:document.getElementById('Longdesc___Frame').style.display='';
		document.getElementById('Longdesc').disabled=false;
		document.getElementById('LongdescSolotext').disabled=true;
		document.getElementById('LongdescSolotext').style.display='none';"/>
<label for="fck">{t}Fckeditor{/t}</label>
<input type="radio" name="editortype" id="textonly" 
	onclick="javascript:document.getElementById('Longdesc___Frame').style.display='none';
		document.getElementById('Longdesc').disabled=true;
		document.getElementById('LongdescSolotext').disabled=false;
		document.getElementById('LongdescSolotext').style.display='';"/>
<label for="textonly">{t}Only text{/t}</label>
{/if}
</fieldset>
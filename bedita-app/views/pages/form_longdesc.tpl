<fieldset>
{if isset($conf->fckeditor)}{$fck->load()}{/if}
<textarea id="Longdesc" name="data[longDesc]" rows="30" cols="30">{if ($object.longDesc)}{$object.longDesc}{/if}</textarea>
{if isset($conf->fckeditor)}{$fck->editor("longdesc")}{/if}
</fieldset>
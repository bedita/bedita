<div class="tab"><h2>{t}Embed Code{/t}</h2></div>

<fieldset id="embed_code">
		
		{if !empty($object.body)}
		<div style="margin-bottom:10px">
			{$object.body|default:''}
		</div>
		{/if}
		<textarea style="width:600px;" name="data[body]">{$object.body|default:''}</textarea>

		<br style="margin-top:10px" />
		{t}thumbnail{/t}: <input type="text" name="data[thumbnail]" value="{$object.thumbnail|default:''}" style="width:600px;"/>

</fieldset>
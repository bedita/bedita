{if ($conf->mce|default:true)}
	
	{$javascript->link("tiny_mce/tiny_mce")}
	{$javascript->link("tiny_mce/tiny_mce_default_init")}


{elseif ($conf->wymeditor|default:true)}

	{$javascript->link("wymeditor/jquery.wymeditor.pack")}
	{$javascript->link("wymeditor/wymeditor_default_init")}

{/if}





<div class="tab"><h2>{t}Text{/t}</h2></div>

<fieldset id="long_desc_langs_container">
	
	<label>{t}short text{/t}:</label>
	<textarea cols="" rows="" name="data[abstract]" style="height:200px" class="mce">{$object.abstract|default:''}</textarea>
	
	<label>{t}long text{/t}:</label>
	<textarea cols="" rows="" name="data[body]" style="height:400px" class="mce">{$object.body|default:''}</textarea>
		
</fieldset>
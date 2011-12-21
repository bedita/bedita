{if ($conf->mce|default:true)}
	
	{$javascript->link("tiny_mce/tiny_mce", false)}
	{assign_concat var='initfile' 1="tiny_mce/tiny_mce_" 2=$conf->mce.init}
	{$javascript->link($initfile, false)}


{elseif ($conf->wymeditor|default:true)}

	{$javascript->link("wymeditor/jquery.wymeditor.pack", false)}
	{$javascript->link("wymeditor/wymeditor_default_init", false)}

{/if}


<div class="tab"><h2>{t}Text{/t}</h2></div>

<fieldset id="long_desc_langs_container">

{if (!empty($addshorttext)) or (!empty($object.abstract))}

		<label>{t}short text{/t}:</label>
		<textarea cols="" rows="" name="data[abstract]" style="height:200px" class="mce abstract">{$object.abstract|default:''}</textarea>
		
		<label for="body">{t}long text{/t}:</label>

{/if}		
		<textarea cols="" rows="" name="data[body]" style="height:{$height|default:200}px" class="mce body">{$object.body|default:''}</textarea>

</fieldset>
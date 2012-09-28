
<div class="tab"><h2>{t}Connect to multimedia gallery{/t}</h2></div>

<fieldset id="frmgallery">
	


{t}Gallery associated to this document{/t}:<br/>

<select id="galleryForDocument" name="data[gallery_id]" style="width:500px;">
	<option value="">{t}No gallery{/t}</option>
	{section name="i" loop=$galleries}
	<option value="{$galleries[i].id}" {if $gallery_id eq $galleries[i].id}selected="selected"{/if}>
		{$galleries[i].title|escape:'quote'} - 
		{$galleries[i].status} - 
		{$galleries[i].created|date_format:$conf->datePattern} - 
		{$galleries[i].lang}
	</option>
	{/section}
</select>

{*
USARE HTMLOPTION DI SMARTY 

*}

</fieldset>
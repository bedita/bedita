<div class="tab"><h2>{t}Previews{/t}</h2></div>
<fieldset id="previewsTab">
{if ($object.status == "off")}
	<ul><li><i>Preview not available: status object is OFF</i></li></ul>
{else}
{foreach from=$previews item="pubs"}

	{if !empty($pubs.object_url.0.public_url)}
		<label>{$pubs.title|escape}</label>
		<ul style="margin-bottom:10px">
		{foreach from=$pubs.object_url item="object_url"}
			{if !empty($object_url.public_url)}
			<li style="border-bottom:1px solid gray; ">
			<a title="{$object_url.public_url}" href="{$object_url.public_url}" target="_blank" rel="#nicknameBEObject">
				{$object_url.public_url|truncate:90:'(...)':true:true}</a>
			</li>
			{/if}
		{/foreach}
		</ul>
	{else}
		<label>{$pubs.title|escape}</label><ul style="margin-bottom:10px"><li style="border-bottom:1px solid gray;"><i>Preview not available: public URL is missing</i></li></ul>
	{/if}

	{if !empty($pubs.object_url.0.staging_url)}	
		<label>{$pubs.title|escape} staging site</label>
		<ul style="margin-bottom:10px">
		{foreach from=$pubs.object_url item="object_url"}
			{if !empty($object_url.staging_url)}
			<li style="border-bottom:1px solid gray; ">
			<a title="{$object_url.staging_url}" href="{$object_url.staging_url}" target="_blank" rel="#nicknameBEObject">
				{$object_url.staging_url|truncate:90:'(…)':true:true}</a>
			</li>
			{/if}
		{/foreach}
		</ul>
	{else}	
		<label>{$pubs.title|escape} staging site</label><ul style="margin-bottom:10px"><li style="border-bottom:1px solid gray;"><i>Preview not available: staging URL is missing</i></li></ul>
	{/if}

{foreachelse}
	
		<i>{t}No publication set{/t}</i>

{/foreach}
{/if}
</fieldset>



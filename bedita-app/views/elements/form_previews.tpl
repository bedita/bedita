<div class="tab"><h2>{t}Previews{/t}</h2></div>

<fieldset id="previewsTab">
	{foreach from=$previews item="pubs"}
	{if $object.status == "on"}
		<label>{$pubs.title}</label>
		<ul style="margin-bottom:10px">
		{foreach from=$pubs.object_url item="object_url"}
			{if !empty($object_url.public_url)}
			<li style="border-bottom:1px solid gray; ">
			<a title="{$object_url.public_url}" href="{$object_url.public_url}" target="_blank" rel="#nicknameBEObject">
				{$object_url.public_url|truncate:90:'(...)':true:true}
				</a>
			</li>
			{/if}
		{/foreach}
		</ul>
	{/if}

	{if $object.status != "off" && $pubs.object_url.0.staging_url|default:''}	
		<label>{$pubs.title} staging site</label>
		<ul>
		{foreach from=$pubs.object_url item="object_url"}
			{if !empty($object_url.staging_url)}
			<li style="border-bottom:1px solid gray; ">
			<a title="{$object_url.staging_url}" href="{$object_url.staging_url}" target="_blank" rel="#nicknameBEObject">
				{$object_url.staging_url|truncate:90:'(…)':true:true}
				</a>
			</li>
			{/if}
		{/foreach}
		</ul>
	{/if}
	{/foreach}
	
</fieldset>



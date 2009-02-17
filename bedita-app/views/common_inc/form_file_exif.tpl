{*
** exif of media item
*}


{if $object.ObjectType.name == "image"}

		{if strpos($object.path,'/') === 0}
			{assign_concat var="fileUrl"  0=$conf->mediaUrl  1=$object.path}
		{else}
			{assign var="fileUrl"  value=$object.path}
		{/if}
		{image_info var="imageInfo" file=$fileUrl}
		
{if $imageInfo.hrtype eq "JPG"}
<div class="tab"><h2>{t}Exif - Main Data{/t}</h2></div>

<fieldset id="exifdata">

	<div style="line-height: 1.4em;">
	{if $imageInfo.exif.main}
		{foreach from=$imageInfo.exif.main item="value" key="key"}
		<span class="label">{$key}</span>: {$value}<br />
		{/foreach}
	{/if}

	{if $imageInfo.exif.XMP}
		<h2 style="margin-top: 10px;">XMP (Adobe) data</h2>
		{section name=XMP loop=$imageInfo.exif.XMP}
		{if $imageInfo.exif.XMP[XMP].value}
		<span class="label">{$imageInfo.exif.XMP[XMP].item}</span>: {$imageInfo.exif.XMP[XMP].value}<br />
		{/if}
		{/section}
	{/if}

	{if !$imageInfo.exif.main && !$imageInfo.exif.XMP}
	EXIF records are empty.
	{/if}
	</div>

</fieldset>

{/if}
{/if}
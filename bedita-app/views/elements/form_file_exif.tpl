{*
** exif of media item
*}


{if $object.ObjectType.name == "image"}

		{if strpos($object.uri,'/') === 0}
			{assign_concat var="fileUrl"  0=$conf->mediaUrl  1=$object.uri}
		{else}
			{assign var="fileUrl"  value=$object.uri}
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

	{if $imageInfo.exif.GPS}
		<h2 style="margin-top: 10px;">GPS data</h2>
		{dump var=$imageInfo.exif.GPS}
	{/if}
	
{*
[GPS] => Array
        (
            [GPSLatitudeRef] => N
            [GPSLatitude] => Array
                (
                    [0] => 45/1
                    [1] => 2610/100
                    [2] => 0/1
                )

            [GPSLongitudeRef] => E
            [GPSLongitude] => Array
                (
                    [0] => 12/1
                    [1] => 1954/100
                    [2] => 0/1
                )

            [GPSTimeStamp] => Array
                (
                    [0] => 18/1
                    [1] => 27/1
                    [2] => 2508/100
                )

            [GPSImgDirectionRef] => T
            [GPSImgDirection] => 124077/454
        )
*}

	{if !$imageInfo.exif.main && !$imageInfo.exif.XMP && !$imageInfo.exif.GPS}
	EXIF records are empty.
	{/if}
	</div>

</fieldset>

{/if}
{/if}
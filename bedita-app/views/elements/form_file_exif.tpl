{*
** exif of media item
*}


{if $object.ObjectType.name == "image" && $object.mime_type != 'image/svg+xml'}

		{if strpos($object.uri,'/') === 0}
			{assign_concat var="fileUrl"  1=$conf->mediaRoot  2=$object.uri}
		{else}
			{assign var="fileUrl"  value=$object.uri}
		{/if}
		{$imgInfo = $imageInfo->get($fileUrl)}
		
{if isset($imgInfo)}
	{if $imgInfo.hrtype eq "JPG"}
<div class="tab"><h2>{t}Exif - Main Data{/t}</h2></div>

<fieldset id="exifdata">

	<div style="line-height: 1.4em;">
	{if $imgInfo.exif.main}
		{foreach from=$imgInfo.exif.main item="value" key="key"}
		<span class="label">{$key}</span>: {$value}<br />
		{/foreach}
	{/if}

	{if $imgInfo.exif.XMP}
		<h2 style="margin-top: 10px;">XMP (Adobe) data</h2>
		{section name=XMP loop=$imgInfo.exif.XMP}
		{if $imgInfo.exif.XMP[XMP].value}
		<span class="label">{$imgInfo.exif.XMP[XMP].item}</span>: {$imgInfo.exif.XMP[XMP].value}<br />
		{/if}
		{/section}
	{/if}

	{if $imgInfo.exif.GPS|default:false}
		<h2 style="margin-top: 10px;">GPS data</h2>
		{dump var=$imgInfo.exif.GPS}
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

	{if !$imgInfo.exif.main && !$imgInfo.exif.XMP && !$imgInfo.exif.GPS|default:false}
	EXIF records are empty.
	{/if}
	</div>

</fieldset>

	{/if}
{/if}
{/if}
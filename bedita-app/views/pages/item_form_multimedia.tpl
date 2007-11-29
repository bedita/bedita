{*
Questo template inserisce un elemento multimediale all'interno de lform
che gestisce gli oggetti multimedia all'interno di una galleria o un documento in genere.

viene utilizzato da: /view/pages/orm_multimedia.tpl.

E' separato perche' puo' essere utilizzato sia al caricamento del form sia con Ajax 
quando si fanno degli upload di file.

parametri:
obj				oggetto da inserire nel form
CACHE			Directory dove thumb inserisce i suoi file 
MEDIA_ROOT		
MEDIA_URL		
thumbWidth		
thumbHeight		
index			Indice posizionamento obj nell'elenco
priority		priorita' dell'oggetto 
cols			numero di colonne
*}
{assign var="thumbWidth" 		value=$thumbWidth|default:100}
{assign var="thumbHeight" 		value=$thumbHeight|default:100}
{assign var="thumbCache" 		value=$CACHE}
{assign var="thumbPath"         value=$MEDIA_ROOT}
{assign var="thumbBaseUrl"      value=$MEDIA_URL}
{assign var="thumbLside"		value=""}
{assign var="thumbSside"		value=""}
{assign var="thumbHtml"			value=""}
{assign var="thumbDev"			value=""}

	{assign var="imagePath" 		value=$obj.path}
	{assign var="imageFile" 		value=$obj.filename|default:$obj.name}
	{assign var="imageTitle" 		value=$obj.title}
	{assign var="newPriority" 		value=$obj.priority+1|default:$priority}
		
	<div id="m_{$obj.id}" class="imageBox">
		<input type="hidden" class="index" 	name="index" value="{$index}" />
		<input type="hidden" class="id" 	name="data[images][{$index}][id]" value="{$obj.id}" />
		<input type="text" class="priority" name="data[images][{$index}][priority]" value="{$obj.priority|default:$priority}" class="priority" size="3" maxlength="3"/>
		<span class="label">{$imageFile}</span>
		<div style="width:{$thumbWidth}px; height:{$thumbHeight}px; overflow:hidden;">
		{if !empty($imageFile) && strtolower($obj.ObjectType.name) == "image"}
			{thumb 
				width="$thumbWidth" 
				height="$thumbHeight" 
				file=$thumbPath$imagePath
				cache="$thumbCache" 
				MAT_SERVER_PATH=$thumbPath 
				MAT_SERVER_NAME=$thumbBaseUrl
				linkurl="$thumbBaseUrl/$imageFile"
				longside="$thumbLside"
				shortside="$thumbSside"
				html="$thumbHtml"
				dev="$thumbDev"} 
		{else}
			<img src="{$session->webroot}/img/image-missing.jpg" width="160"/>
		{/if}
		</div>
		<br/>
		{t}Title{/t}:<br/>
		{$imageTitle|escape:'htmlall'}
		<br/>
		{t}Description{/t}:<br/>
		{$obj.shortDesc|escape:'htmlall'}
		<br/>
		{t}Size{/t}:<br/>
		{$obj.size/1000} Kb
		<br/>
		{if !empty($imageFile) && $obj.name == "Image"}
		x: {$obj.width} y: {$obj.height}
		{/if}
		<div align="right" style="padding-top:4px; margin-top:4px; border-top:1px solid silver">
		<input type="button" onclick="removeItemMultimedia('m_{$obj.id}')" value="{t}Delete{/t}" />
		</div>
	</div>


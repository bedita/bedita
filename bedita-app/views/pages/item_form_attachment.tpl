{*
Questo template inserisce un alegato all'interno del form
che gestisce gli allegati all'interno di un documento in genere.

viene utilizzato da: /view/pages/form_attachments.tpl.

E' separato perche' puo' essere utilizzato sia al caricamento del form sia con Ajax 
quando si fanno degli upload di file.

parametri:
obj				oggetto da inserire nel form
CACHE			Directory dove thumb inserisce i suoi file 
MEDIA_ROOT		
MEDIA_URL		
index			Indice posizionamento obj nell'elenco
priority			priorita' dell'oggetto 
cols			numero di colonne
*}
{assign var="thumbWidth" 		value=$thumbWidth|default:10}
{assign var="thumbHeight" 		value=$thumbHeight|default:10}
{assign var="thumbCache" 		value=$CACHE}
{assign var="thumbPath"     		    value=$MEDIA_ROOT}
{assign var="thumbBaseUrl"      	value=$MEDIA_URL}
{assign var="thumbLside"		value=""}
{assign var="thumbSside"		value=""}
{assign var="thumbHtml"			value=""}
{assign var="thumbDev"			value=""}

	{assign var="attachPath" 		value=$obj.path}
	{assign var="attachFile" 		value=$obj.filename|default:$obj.name}
	{assign var="attachTitle" 		value=$obj.title}
	{assign var="newPriority" 		value=$obj.priority+1|default:$priority}
		
	<div id="m_{$obj.id}" class="attachBox">
		<input type="hidden" class="index" 	name="index" value="{$index}" />
		<input type="hidden" class="id" 	name="data[attachments][{$index}][id]" value="{$obj.id}" />
		<input type="text" class="priority" name="data[attachments][{$index}][priority]" value="{$obj.priority|default:$priority}" size="3" maxlength="3"/>
		<span class="label">{$attachFile}</span>
		<div style="width:{$thumbWidth}px; height:{$thumbHeight}px; overflow:hidden;">
		</div>
		<br/>
		{t}Title{/t}:<br/>
		{$attachTitle|escape:'htmlall'}
		<br/>
		{t}Description{/t}:<br/>
		{$obj.short_desc|escape:'htmlall'}
		<br/>
		{t}Size{/t}:<br/>
		{$obj.size/1000} Kb
		<br/>
		<div align="right" style="padding-top:4px; margin-top:4px; border-top:1px solid silver">
		<input type="button" onclick="removeItemAttachment('m_{$obj.id}')" value="{t}Delete{/t}" />
		</div>
	</div>


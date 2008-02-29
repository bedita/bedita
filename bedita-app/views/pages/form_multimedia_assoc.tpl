<!-- start upload block-->
<script type="text/javascript">
<!--
{literal}
function addItemsToParent() {
	var itemsIds = new Array() ;
	$(":checkbox").each(function() {
		try {
			if(this.checked && this.name == 'chk_bedita_item') { itemsIds[itemsIds.length] = $(this).attr("value") ;}
		} catch(e) {
		}
	}) ;
	for(i=0;i<itemsIds.length;i++) {
		$("#tr_"+itemsIds[i]).remove();
	}
	try {
		{/literal}{$controller}{literal}CommitUploadById(itemsIds) ;
	} catch(e) {
		parent.{/literal}{$controller}{literal}CommitUploadById(itemsIds) ;
	}
	$('#container-1').triggerTab(1);
}
$(document).ready(function(){
	$(".selItems").bind("click", function(){
		var check = $("input:checkbox",$(this).parent().parent()).get(0).checked ;
		$("input:checkbox",$(this).parent().parent()).get(0).checked = !check ;
	}) ;
	/* select/unselect each item's checkbox */
	$(".selectAll").bind("click", function(e) {
		var status = this.checked;
		$(".itemCheck").each(function() { this.checked = status; });
	}) ;
	/* select/unselect main checkbox if all item's checkboxes are checked */
	$(".itemCheck").bind("click", function(e) {
		var status = true;
		$(".itemCheck").each(function() { if (!this.checked) return status = false;});
		$(".selectAll").each(function() { this.checked = status;});
	}) ;
});
//-->
{/literal}
</script>
</head>
<body>
<div>
	<fieldset>
		{if !empty($items)}
		<p>{t}Total number of{/t} {t}{$itemType} items{/t}: {$beToolbar->size()}</p>
<p>&nbsp;</p>{*		DA INSERIRE TOOLBAR DI NAVIGAZIONE
<p class="toolbar">{t}{$itemType}{/t}: {$beToolbar->size()}</p>
*}
		<table class="indexList">
		<tr>
			<th><input type="checkbox" class="selectAll" id="selectAll"><label for="selectAll"> (Un)Select All</label></th>
			<th>{$beToolbar->order('id', 'id')}</th>
			{*
			<th>{$beToolbar->order('title', 'Title')}</th>
			<th>{$beToolbar->order('status', 'Status')}</th>
			<th>{$beToolbar->order('created', 'Created')}</th>
			<th>{t}Type{/t}</th>
			*}
			<th>{t}Thumb{/t}</th>
			<th>{t}File name{/t}</th>
			{*<th>{t}MIME type{/t}</th>*}
			<th>{t}File size{/t}</th>
			<th>{$beToolbar->order('lang', 'Language')}</th>
		</tr>

		{foreach from=$items item='mobj' key='mkey'}
		<tr class="rowList" id="tr_{$mobj.id}">
			<td><input type="checkbox" value="{$mobj.id}" name="chk_bedita_item" class="itemCheck"/></td>
			<td><a class="selItems" href="javascript:void(0);">{$mobj.id}</a></td>
			{*
			<td>{$mobj.title}</td>
			<td>{$mobj.status}</td>
			<td>{$mobj.created|date_format:'%b %e, %Y'}</td>
			<td>{$mobj.bedita_type|default:""}</td>
			*}
			<td>
{assign var="thumbWidth" 		value=30}
{assign var="thumbHeight" 		value=30}
{assign var="thumbPath"         value=$conf->mediaRoot}
{assign var="thumbBaseUrl"      value=$conf->mediaUrl}
{assign var="thumbLside"		value=""}
{assign var="thumbSside"		value=""}
{assign var="thumbHtml"			value=""}
{assign var="thumbDev"			value=""}
{assign var="filePath"			value=$mobj.path}
{if strtolower($mobj.ObjectType.name) == "image"}
	{thumb 
		width="$thumbWidth" 
		height="$thumbHeight"
		file=$thumbPath$filePath
		cache=$conf->imgCache 
		MAT_SERVER_PATH=$conf->mediaRoot 
		MAT_SERVER_NAME=$conf->mediaUrl
		linkurl="$thumbBaseUrl$filePath"
		longside="$thumbLside"
		shortside="$thumbSside"
		html="$thumbHtml"
		dev="$thumbDev"}
{else}
	<div><a href="{$conf->mediaUrl}{$filePath}" target="_blank"><img src="{$session->webroot}img/mime/{$mobj.type}.gif" /></a> </div>
{/if}
			</td>
			<td>{$mobj.name|default:""}</td>
			{*<td>{$mobj.type|default:""}</td>*}
			<td>{$mobj.size|default:""}</td>
			<td>{$mobj.lang}</td>
		</tr>
		{/foreach}
{*
		<tr>
			<td colspan="10">
				<input class="selectAll" type="button" value="O - {t}Select all{/t}"/>
				<input class="unselectAll" type="button" value="/ - {t}Unselect all{/t}"/>
			</td>
		</tr>
*}
		<tr>
			<td colspan="10">
				<input type="button" onclick="javascript:addItemsToParent();" value=" (+) {t}Add selected items{/t}"/>
			</td>
		</tr>
		</table>
		
{*		DA INSERIRE TOOLBAR DI NAVIGAZIONE
<p class="toolbar">{t}{$itemType}{/t}: {$beToolbar->size()}</p>
*}

		{else}
		{t}No {$itemType} found{/t}
		{/if}
	</fieldset>
	
</div>
<!-- end upload block -->
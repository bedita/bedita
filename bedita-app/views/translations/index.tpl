<script type="text/javascript">
var urlDelete = "{$html->url('deleteTranslations/')}" ;
var message = "{t}Are you sure that you want to delete the item?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected items?{/t}" ;
var URLBase = "{$html->url('index/')}" ;

{literal}
$(document).ready(function() {

	$("TABLE.indexlist TD.cellList").click(function(i) { 
		document.location = $(this).parent().find("a:first").attr("href"); 
	} );

	/* select/unselect each item's checkbox */
	$(".selectAll").bind("click", function(e) {
		var status = this.checked;
		$(".objectCheck").each(function() { this.checked = status; });
	}) ;
	/* select/unselect main checkbox if all item's checkboxes are checked */
	$(".objectCheck").bind("click", function(e) {
		var status = true;
		$(".objectCheck").each(function() { if (!this.checked) return status = false;});
		$(".selectAll").each(function() { this.checked = status;});
	}) ;

	$("#deleteSelected").bind("click", delObjects);
	$("a.delete").bind("click", function() {
		delObject($(this).attr("title"));
	});

	$("#changestatusSelected").bind("click",changeStatusTranslations);

});

function delObject(id) {
	if(!confirm(message)) return false ;
	$("#objects_selected").attr("value",id);
	$("#formObject").attr("action", urlDelete) ;
	$("#formObject").get(0).submit() ;
	return false ;
}
function delObjects() {
	if(!confirm(messageSelected)) return false ;
	var oToDel = "";
	var checkElems = document.getElementsByName('object_chk');
	for(var i=0;i<checkElems.length;i++) { if(checkElems[i].checked) oToDel+= ","+checkElems[i].title; }
	oToDel = (oToDel=="") ? "" : oToDel.substring(1);
	$("#objects_selected").attr("value",oToDel);
	$("#formObject").attr("action", urlDelete) ;
	$("#formObject").get(0).submit() ;
	return false ;
}
function changeStatusTranslations() {
	var status = $("#newStatus").val();
	if(status != "") {
		var oToDel = "";
		var checkElems = document.getElementsByName('object_chk');
		for(var i=0;i<checkElems.length;i++) { if(checkElems[i].checked) oToDel+= ","+checkElems[i].title; }
		oToDel = (oToDel=="") ? "" : oToDel.substring(1);
		$("#objects_selected").attr("value",oToDel);
		$("#formObject").attr("action", '{/literal}{$html->url('changeStatusTranslations/')}{literal}' + status) ;
		$("#formObject").get(0).submit() ;
		return false ;
	}
}
{/literal}
</script>
</head>

<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" method="index" fixed=true}

{include file="../common_inc/toolbar.tpl"}

<div class="mainfull">
	
	<form method="post" action="{$html->url('/translations/index')}" id="formObject">

	<input type="hidden" name="data[id]" value="{$object_translation.id.status|default:''}"/>
	<input type="hidden" name="data[master_id]" value="{$object_master.id|default:''}"/>
	<input type="hidden" name="objects_selected" id="objects_selected"/>
	

	<table class="indexlist">
	{capture name="theader"}
		<tr>
			<th></th>
			<th>{$beToolbar->order('title', 'master title')}</th>
			<th>{$beToolbar->order('LangText.title', 'title')}</th>
			<th>{$beToolbar->order('object_type_id', 'Type')}</th>
			<th>{$beToolbar->order('LangText.lang', 'Language')}</th>
			<th>{$beToolbar->order('LangText.status', 'Status')}</th>
		</tr>
	{/capture}

		{$smarty.capture.theader}
	
		{section name="i" loop=$translations}
		
		{assign var="oid" value=$translations[i].LangText.object_id}
		{assign var="olang" value=$translations[i].LangText.lang}
		{assign var="ot" value=$translations[i].BEObject.object_type_id}
		{assign var="mtitle" value=$translations[i].BEObject.title}
		
		<tr class="obj {$translations[i].LangText.status}">
			<td style="width:15px; padding:7px 0px 0px 0px;">
				<input  type="checkbox" name="object_chk" class="objectCheck" title="{$translations[i].LangText.id}" />
			</td>
			<td>
				{$mtitle|truncate:38:true} &nbsp;
			</td>
			<td><a href="{$html->url('view/')}{$oid}/{$olang}">{$translations[i].LangText.title|default:'<em>no title</em>'|truncate:38:true}</a></td>
			<td>
				<span class="listrecent {$conf->objectTypes[$ot].model|lower}">&nbsp;</span>
				{$conf->objectTypes[$ot].model}
			</td>
			<td>{$olang}</td>
			<td>{$translations[i].LangText.status}</td>
		</tr>
		{sectionelse}
			<tr><td colspan="100" style="padding:30px">{t}No {$moduleName} found{/t}</td></tr>
		{/section}

{if ($smarty.section.i.total) >= 10}
	{$smarty.capture.theader}
{/if}


</table>

<br />


	
<div class="tab"><h2>{t}filters{/t}</h2></div>
<div>
	{t}Show translations in{/t}: &nbsp;
	<select name="data[translation_lang]">
		<option value=""></option>
	{foreach key=val item=label from=$conf->langOptions}
		<option value="{$val}"{if $langSelected==$val} selected="selected"{/if}>{$label}</option>
	{/foreach}
	</select>
	
	&nbsp;{t}with status{/t}: &nbsp;
	<select name="data[translation_status]">
	<option value=""></option>
	<option value="on"{if $statusSelected=='on'} selected="selected"{/if}>{t}on{/t}</option>
	<option value="off"{if $statusSelected=='off'} selected="selected"{/if}>{t}off{/t}</option>
	<option value="draft"{if $statusSelected=='draft'} selected="selected"{/if}>{t}draft{/t}</option>
	<option value="required"{if $statusSelected=='required'} selected="selected"{/if}>{t}required{/t}</option>
	</select>
	
	&nbsp;{t}of master id{/t}:&nbsp;
	<input type="text" name="data[translation_object_id]" style="width:25px"
	value="{$objectIdSelected}"/>
	&nbsp;<input type="submit" value="{t}go{/t}"/>
	
{if !empty($translations)}
	<hr />
		{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')} 
		&nbsp;&nbsp;&nbsp;
		{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;
{/if}
	
	</div>

{if !empty($translations)}
	
	<div class="tab"><h2>Operations on above records</h2></div>
	<div>
		<label for="selectAll"><input type="checkbox" class="selectAll" id="selectAll"/> {t}(un)select all{/t}</label>
		<hr />
		{t}change status to:{/t}	<select style="width:75px" id="newStatus" data="newStatus">
									<option value=""> -- </option>
									<option value="on"> ON </option>
									<option value="off"> OFF </option>
									<option value="draft"> DRAFT </option>
									<option value="required"> REQUIRED </option>
								</select>
				<input id="changestatusSelected" type="button" value=" ok " />
		<hr />
		{t}delete selected items{/t}&nbsp;<input id="deleteSelected" type="button" value=" ok "/>
		<hr />
	</div>
{/if}

</form>
</div>
{$html->css('module.tags')}
{$html->css('tree')}
{$javascript->link("jquery.treeview")}
{$javascript->link("interface")}
{$javascript->link("module.tags")}
{$javascript->link("form")}
{$javascript->link("jquery.changealert")}

</head>
<body>
{include file="head.tpl"}
<div id="centralPage">	
{include file="submenu.tpl" method="index"}	
<script type="text/javascript">
<!--
var urlDelete = "{$html->url('delete/')}" ;
var message = "{t}Are you sure that you want to delete the tag?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected tags?{/t}" ;
var URLBase = "{$html->url('index/')}" ;
{literal}
$(document).ready(function() {
	$("TABLE.indexList TD.cellList").click(function(i) { 
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
	
});
function delObject(id) {
	if(!confirm(message)) return false ;
	$("#objects_to_del").attr("value",id);
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
	$("#objects_to_del").attr("value",oToDel);
	$("#formObject").attr("action", urlDelete) ;
	$("#formObject").get(0).submit() ;
	return false ;
}
{/literal}
//-->
</script>	
<div id="containerPage">
	<div id="listElements">
	<form method="post" action="" id="formObject">
	<fieldset>
	<input type="hidden" name="data[id]"/>
	<input type="hidden" name="objects_to_del" id="objects_to_del"/>
	{if $objects}
	{assign var="pagParams" value=$paginator->params()}
	{assign_associative var="optionsPag" class=""}
	{assign_associative var="optionsPagDisable" class=""}
	<p class="toolbar">
		{t}{$moduleName|capitalize}{/t}: {t}page{/t} {$paginator->counter()} {$paginator->numbers()} &nbsp;
		{*
		{assign_associative var="pass" page="1"}
		{$paginator->link('first', $pass, $optionsPag)} {$paginator->prev('prev', $optionsPag, 'no prev', $optionsPagDisable)} {$paginator->next('next >', $optionsPag, null, $optionsPagDisable)} &nbsp;
		{if $paginator->hasNext()}{assign_associative var="pass" page=$pagParams.pageCount}{$paginator->link('last', $pass, $optionsPag)}{/if} &nbsp;
		*}
		{t}Dimensions{/t}:
		<select name="limit" onchange="javascript: changelimit(this.value);">
			<option>&nbsp;</option>
			<option value="10"{if $pagParams.current == 10} selected{/if}>10</option>
			<option value="30"{if $pagParams.current == 30} selected{/if}>30</option>
			<option value="50"{if $pagParams.current == 50} selected{/if}>50</option>
		</select>
	</p>
	<table class="indexList">
	<thead>
	<tr>
		<th><input type="checkbox" class="selectAll" id="selectAll"/><label for="selectAll"> {t}(Un)Select All{/t}</label></th>
		<th>Id</th>
		<th>{$paginator->sort('Name', 'label')}</th>
		<th>{$paginator->sort('Status', 'status')}</th>
		<th>&nbsp;</th>
	</tr>
	</thead>
	<tbody>
	{section name="i" loop=$objects}
	<tr class="rowList">
		<td><input type="checkbox" name="object_chk" class="objectCheck" title="{$objects[i].id}"/></td>
		<td class="cellList"><a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].id}</a></td>
		<td class="cellList">{$objects[i].label}</td>
		<td class="cellList">{$objects[i].status}</td>
		<td><a href="javascript:void(0);" class="delete" title="{$objects[i].id}">{t}Delete{/t}</a></td>
	</tr>
	{/section}
	<tr><td colspan="7"><input id="deleteSelected" type="button" value="X - {t}Delete selected items{/t}"/></td></tr>
	</tbody>
	</table>
	{else}
	{t}No {$moduleName} found{/t}
	{/if}
	</fieldset>
	</form>
	</div>
</div>
</div>
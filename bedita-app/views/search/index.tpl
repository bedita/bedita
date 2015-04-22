{$html->css("tree", null, ['inline' => false])}
{$html->script("form", false)}

<script type="text/javascript">
<!--
var urlDelete = "{$html->url('delete/')}" ;
var message = "{t}Are you sure that you want to delete the tag?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected tags?{/t}" ;
var URLBase = "{$html->url('index/')}" ;

$(document).ready(function() {

	$("#deleteSelected").bind("click", delObjects);
	$("a.delete").bind("click", function() {
		delObject($(this).prop("title"));
	});
	
});
function delObject(id) {
	if (!confirm(message)) {
		return false;
	}
	$("#objects_selected").val(id);
	$("#formObject").prop("action", urlDelete);
	$("#formObject").get(0).submit();
	return false;
}
function delObjects() {
	if(!confirm(messageSelected)) return false;
	var oToDel = "";
	var checkElems = document.getElementsByName('object_chk');
	for (var i = 0; i < checkElems.length; i++) {
		if (checkElems[i].checked) {
			oToDel += "," + checkElems[i].title;
		}
	}
	oToDel = (oToDel=="") ? "" : oToDel.substring(1);
	$("#objects_selected").val(oToDel);
	$("#formObject").prop("action", urlDelete);
	$("#formObject").get(0).submit();
	return false;
}

//-->
</script>

{include file="modulesmenu.tpl"}

{include file = './inc/menuleft.tpl' method = 'index'}

{include file = './inc/menucommands.tpl' method = 'index'}

{include file="../toolbar.tpl"}

<div class="main">

<form method="post" action="" id="formObject">
	{$beForm->csrf()}

	<input type="hidden" name="data[id]"/>
	<input type="hidden" name="objects_selected" id="objects_selected"/>

	
	{assign var="pagParams" value=$paginator->params()}
	{assign_associative var="optionsPag" class=""}
	{assign_associative var="optionsPagDisable" class=""}

	
	<table class="indexlist">

	<tr>
		<th>{$paginator->sort('Name', 'label')}</th>
		<th>{$paginator->sort('Status', 'status')}</th>
		<th class="center">{$paginator->sort('Occurrences', 'occurences')}</th>
		<th>Id</th>
		<th></th>
	</tr>

	{section name="i" loop=$objects}
		<tr>
			<td>
				<input type="checkbox" name="object_chk" class="objectCheck" title="{$objects[i].id}"/>
				&nbsp;&nbsp;
				<a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].label|escape}</a>
				
			</td>
			<td>{$objects[i].status}</td>
			<td class="center">33</td>
			<td><a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].id}</a></td>
			<td><a href="{$html->url('view/')}{$objects[i].id}">{t}details{/t}</a></td>
		</tr>
	{sectionelse}
	
		<tr><td colspan="100" style="padding:30px">{t}No items found{/t}</td></tr>
	
	{/section}

	</table>


	<div class="tab"><h2>{t}operations{/t}</h2></div>
	<div>
		<input type="checkbox" class="selectAll" id="selectAll"/><label for="selectAll"> {t}(Un)Select All{/t}</label>
		<hr />
		
		<input id="deleteSelected" type="button" value="{t}Delete selected items{/t}"/>
		
		<input id="changeStatus" type="button" value="{t}change status{/t}"/>
		
		<hr />
		
		<textarea name="addtaglist" id="addtaglist"></textarea>
		<br><input id="addmultipletag" type="button" value="{t}add more tags{/t}"/> 
		{t}Add comma separated words{/t}
		
	</div>


</form>

</div>

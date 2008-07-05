
{$javascript->link("form", false)}

</head>

<body>
{literal}
<script type="text/javascript">
<!--
var urlDelete = "{$html->url('delete/')}" ;
var message = "{t}Are you sure that you want to delete the tag?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected tags?{/t}" ;
var URLBase = "{$html->url('index/')}" ;

$(document).ready(function() {

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

	$("#taglist").hide();
	
	$(".tagToolbar.viewlist").click(function () {
		$("#taglist").show();
		$("#tagcloud").hide();
	});
	$(".tagToolbar.viewcloud").click(function () {
		$("#taglist").hide();
		$("#tagcloud").show();
	});

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



//-->
</script>
{/literal}

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" method="index"}

{include file="../common_inc/toolbar.tpl"}

<div class="main">

<form method="post" action="" id="formObject">

	<input type="hidden" name="data[id]"/>
	<input type="hidden" name="objects_selected" id="objects_selected"/>

	
	{assign var="pagParams" value=$paginator->params()}
	{assign_associative var="optionsPag" class=""}
	{assign_associative var="optionsPagDisable" class=""}

		
				
	<table class="indexlist">

	<tr>
		
		<th style="width:45px;">
			
			<img class="tagToolbar viewcloud" src="/img/iconML-cloud.png" />
			<img class="tagToolbar viewlist" src="/img/iconML-list.png" />
			
		</th>
		
		<th>
			{$paginator->sort('Name', 'label')}
		</th>
		<th>{$paginator->sort('Status', 'status')}</th>
		<th class="center">{$paginator->sort('Ocurrences', 'occurences')}</th>
		<th>Id</th>
		<th></th>
	</tr>
	<tbody id="taglist">
	{section name="i" loop=$objects}
		<tr>
			<td style="width:36px; text-align:center">
				<input type="checkbox" name="object_chk" class="objectCheck" title="{$objects[i].id}"/>
			</td>
			<td>
				<a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].label}</a>
				
			</td>
			<td>{$objects[i].status}</td>
			<td class="center">33</td>
			<td><a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].id}</a></td>
			<td><a href="{$html->url('view/')}{$objects[i].id}">{t}details{/t}</a></td>
		</tr>
	{sectionelse}
	
		<tr><td colspan="100" style="padding:30px">{t}No {$moduleName} found{/t}</td></tr>
	
	{/section}
	</tbody>
	
	<tbody id="tagcloud">
		<tr>
			<td colspan="10" class="tag graced" style="text-align:justify; line-height:1.5em; padding:20px;">
				{section name="i" loop=$objects}
				<a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].label}</a>
				{/section}
			</td>
		</tr>
		
	</tbody>
	
	</table>


	<br />
	<div class="tab"><h2>{t}operazioni{/t}</h2></div>
	<div>

		<label for="selectAll"><input type="checkbox" class="selectAll" id="selectAll"/> {t}(un)select all{/t}</label>
		
	&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
	{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')} 
	&nbsp;&nbsp;&nbsp;
	{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;
	&nbsp;&nbsp;&nbsp
		
		<hr>
{t}change status to:{/t} 	<select style="width:75px" id="newStatus" data="newStatus">
									<option value=""> -- </option>
									<option> ON </option>
									<option> OFF </option>
									<option> DRAFT </option>
								</select>
			<input id="changestatusSelected" type="button" value=" ok " />
	<hr />


	
	<input id="deleteSelected" type="button" value="X {t}Delete selected items{/t}"/>
		<hr />
		
		<textarea name="addtaglist" id="addtaglist"></textarea>
		<br><input id="addmultipletag" type="button" value="{t}add more tags{/t}"/> 
		{t}Add comma separated words{/t}
		
	</div>


</form>

</div>


<script type="text/javascript">
<!--
var urlDelete = "{$html->url('delete/')}" ;
var message = "{t}Are you sure that you want to delete the item?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected items?{/t}" ;
var URLBase = "{$html->url('index/')}" ;
{literal}
$(document).ready(function(){


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
{if !empty($tree)}
{literal}
function assocObjectsToAreaSection(id) {
	var oToDel = "";
	var checkElems = document.getElementsByName('object_chk');
	for(var i=0;i<checkElems.length;i++) { if(checkElems[i].checked) oToDel+= ","+checkElems[i].title; }
	oToDel = (oToDel=="") ? "" : oToDel.substring(1);
	$("#objects_to_del").attr("value",oToDel);
	$("#formObject").attr("action", '{/literal}{$html->url('addToAreaSection/')}{literal}') ;
	$("#formObject").get(0).submit() ;
	return false ;
}
{/literal}
{/if}
//-->
</script>	


	
	
	<form method="post" action="" id="formObject">

	<input type="hidden" name="data[id]"/>
	<input type="hidden" name="objects_to_del" id="objects_to_del"/>


	<table class="indexlist">

		<tr>
			<th></th>
			<th>{$beToolbar->order('title', 'Title')}</th>
			<th>{$beToolbar->order('id', 'id')}</th>
			<th>{$beToolbar->order('status', 'Status')}</th>
			<th>{$beToolbar->order('modified', 'Modified')}</th>		
			<th>{$beToolbar->order('lang', 'Language')}</th>
		</tr>
	
	
		{section name="i" loop=$objects}
		
		<tr>
			<td style="width:15px; padding:7px 0px 0px 0px;">
				<input  type="checkbox" 
				name="object_chk" class="objectCheck" title="{$objects[i].id}" />
			</td>
			<td><a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].title|truncate:64}</a></td>
			<td>{$objects[i].id}</td>
			<td>{$objects[i].status}</td>
			<td>{$objects[i].modified|date_format:'%d %B %Y %H:%M'}</td>
			<td>{$objects[i].lang}</td>
		</tr>
		
		{sectionelse}
		
			<tr><td colspan="100" style="padding:30px">{t}No {$moduleName} found{/t}</td></tr>
		
		{/section}
		
	
</table>


<br />
<br />

<div class="tab"><h2>Operazioni sui 3 records selezionati</h2></div>
<div>
	<input type="checkbox" class="selectAll" id="selectAll"/><label for="selectAll"> {t}(Un)Select All{/t}</label>
	<hr />
	<input id="deleteSelected" type="button" value="X {t}Delete selected items{/t}"/>

	{if !empty($tree)}
			<input id="deleteSelected" type="button" value="(+) {t}Add selected items to area/section{/t}" 
			onclick="javascript:assocObjectsToAreaSection();"/>
			<select id="areaSectionAssoc" class="areaSectionAssociation" name="data[destination]">
			{foreach from=$tree item=i}
				<option value="{$i.id}">{$i.title}</option>
				{if !empty($i.children)}
					{foreach from=$i.children item=ii}
					<option value="{$ii.id}">-- {$ii.title}</option>
					{if !empty($ii.children)}
						{foreach from=$ii.children item=iii}
						<option value="{$iii.id}">--- {$iii.title}</option>
						{/foreach}
					{/if}
					{/foreach}
				{/if}
			{/foreach}
			</select>
	{/if}
	
</div>
	
	</form>




